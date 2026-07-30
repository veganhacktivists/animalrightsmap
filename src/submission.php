<?php

// The decision-making behind the group-submission endpoint, kept apart from
// the request handling in public/mail.php so it can be tested directly.

/**
 * Resolve the IP the rate limiter keys on.
 *
 * Traefik appends the real peer to X-Forwarded-For, so the last entry is the
 * one a client cannot spoof by sending a header of its own.
 */
function arm_client_ip(array $server): string
{
    if (isset($server['HTTP_X_FORWARDED_FOR'])) {
        $parts = array_map('trim', explode(',', $server['HTTP_X_FORWARDED_FOR']));

        return end($parts);
    }

    return $server['REMOTE_ADDR'] ?? 'unknown';
}

/**
 * Trim the submitted fields and validate the required ones. Returns the
 * cleaned values, or null when the submission is incomplete.
 */
function arm_validate_submission(array $post): ?array
{
    $fields = [];
    foreach (['name', 'email', 'groupNames', 'socialMediaLinks', 'regions', 'message'] as $key) {
        $fields[$key] = trim($post[$key] ?? '');
    }

    $email = filter_var($fields['email'], FILTER_VALIDATE_EMAIL);

    if ($email === false
        || $fields['name'] === ''
        || $fields['groupNames'] === ''
        || $fields['socialMediaLinks'] === ''
        || $fields['regions'] === '') {
        return null;
    }

    $fields['email'] = $email;

    return $fields;
}

/**
 * Build the HTML body, escaping every user-supplied value.
 */
function arm_submission_html(array $fields): string
{
    $escape = fn ($value) => htmlspecialchars($value, ENT_QUOTES, 'UTF-8');

    return '<b>Name</b>: '.$escape($fields['name']).'<br>'
        .'<b>Email</b>: '.$escape($fields['email']).'<br>'
        .'<b>Group Name(s)</b>: '.$escape($fields['groupNames']).'<br>'
        .'<b>Social Media Link(s)</b>: '.$escape($fields['socialMediaLinks']).'<br>'
        .'<b>City/Region(s)</b>: '.$escape($fields['regions']).'<br>'
        .'<b>Message</b>: '.nl2br($escape($fields['message'])).'<br>';
}
