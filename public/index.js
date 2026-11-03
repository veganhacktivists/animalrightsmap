async function sendEmail (data) {
  // Send the raw form fields in the request body. The server fixes the
  // recipient, sender and subject itself, so we never pass those from here.
  const result = await axios.post('/mail.php', new URLSearchParams(data));

  if (result.data !== 'OK') {
    throw new Error(result.data);
  }
}

function displayError (error) {
  Swal.showValidationMessage(error);
}

async function openGroupSubmissionModal () {
  await Swal.fire({
    template: '#group-submission-modal',
    showLoaderOnConfirm: true,
    allowOutsideClick: () => !Swal.isLoading(),
    preConfirm: async () => {
      const form = document.getElementById("group-submission-form")
      if (!form.checkValidity()) {
        form.reportValidity()
        displayError('Please double-check your email address and make sure all required fields are filled in.');

      } else {
        const name = document.getElementById('name').value
        const email = document.getElementById('email').value
        const groupNames = document.getElementById('group-names').value
        const socialMediaLinks = document.getElementById('social-media-links').value
        const regions = document.getElementById('regions').value
        const message = document.getElementById('message').value

        try {
          await sendEmail({
            name,
            email,
            groupNames,
            socialMediaLinks,
            regions,
            message,
          });

        } catch (error) {
          displayError('Oops! Something went wrong. Try reaching us directly at map@veganhacktivists.org');
        }
      }
    }
  }).then((result) => {
    if (result.isConfirmed) {
      Swal.fire({
        template: '#group-submission-success',
      })
    }
  });
}
