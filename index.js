async function main() {
  let qs = parseQueryString(document.location.search)
  console.log(qs)
  // demo activist hub data with the appropriate querystring flag - otherwise, use umap data
  if (qs.backend && validBackends[qs.backend]) {
    document.getElementById('umap').style.display = 'none';
    document.getElementById('ahmap').style.display = 'block';
    buildActivistHubMap('ahmap', qs.backend)
  }
}

const validBackends = Object.fromEntries([
  'http://activisthub.local',
  'http://127.0.0.1',
  'http://localhost',
  'https://staging.activisthub.org',
  'https://activisthub.org',
  'https://www.activisthub.org',
].map(b => [b, true]))

async function buildActivistHubMap(el, backend) {
  const Leaflet = L
  const map = Leaflet.map(el).setView([51.505, -0.09], 4);
  Leaflet.tileLayer('https://cartodb-basemaps-{s}.global.ssl.fastly.net/light_all/{z}/{x}/{y}.png', {})
    .addTo(map);

  const groups = await fetchGroups(backend)
  const byOrg = {}
  for (let group of groups.filter(c => !!c.location)) {
    const orgId = group.organisation.id
    if (!byOrg[orgId]) {
      //const color = '#'+([30,70,110].map(f =>
      //  ((f * (orgId+0)) % 256).toString(16)
      //).join(''))
      // https://learnui.design/blog/the-hsb-color-system-practicioners-primer.html
      const hue = (180+37) * (orgId+0) % 360
      const color = `hsl(${hue}, 100%, 50%)`
      const textColor = hue > 200 ? 'white' : 'black'
      const org = byOrg[orgId] = {
        id: orgId,
        color,
        textColor,
        groups: [],
      }
      console.log(org)
      console.log('%c '+org.color, 'color: '+org.color)
    }
    byOrg[orgId].groups.push(group)
  }
  const orgs = Object.values(byOrg).sort((a,b) => a.id - b.id)

  for (let org of orgs) {
    const markers = Leaflet.markerClusterGroup({
      polygonOptions: {
        color: org.color,
      },
      iconCreateFunction: function (cluster) {
        return Leaflet.divIcon({
          className: 'leaflet-marker-icon marker-cluster',
          iconSize: [40,40],
          html: `
              <div style="background-color: ${org.color}; color: ${org.textColor}">
                <span>${cluster.getChildCount()}</span>
              </div>
          `,
        })
      },
    });
    for (let group of org.groups) {
      let address = [group.location.city, group.location.state].join(', ')

      let popup = Leaflet.DomUtil.create('div', '')
      let popupLink = Leaflet.DomUtil.create('p', '', popup)
      let popupLink1 = Leaflet.DomUtil.create('div', '', popupLink)
      let popupLink2 = Leaflet.DomUtil.create('div', '', popupLink)
      let popupLink3 = Leaflet.DomUtil.create('div', '', popupLink)
      popupLink1.textContent = group.name
      popupLink2.textContent = group.organisation.name
      popupLink3.textContent = group.location.city
      let popupDesc = Leaflet.DomUtil.create('p', '', popup)
      popupDesc.textContent = address

      let tooltip = Leaflet.DomUtil.create('div', '')
      let tooltip1 = Leaflet.DomUtil.create('div', '', tooltip)
      let tooltip2 = Leaflet.DomUtil.create('div', '', tooltip)
      let tooltip3 = Leaflet.DomUtil.create('div', '', tooltip)
      tooltip1.textContent = group.display_name
      tooltip2.textContent = group.organisation.name
      tooltip3.textContent = address

      // https://github.com/umap-project/umap/blob/88cd3e8cf0b24516ba62fbea4a7f58e6038e2e0f/umap/static/umap/js/umap.icon.js
      let marker = Leaflet.marker([group.location.latitude, group.location.longitude], {
        icon: Leaflet.divIcon({
          iconAnchor: [16, 40],
          popupAnchor: [0, -40],
          tooltipAnchor: [16, -24],
          className: 'explore-map-icon',
          html: `
            <div class="icon_container" style="background-color: ${org.color};">
              <img src="/marker.png">
            </div>
            <div class="icon_arrow" style="border-top-color: ${org.color};"></div>
          `
        }),
      })
      .bindPopup(popup)
      .bindTooltip(tooltip)

      markers.addLayer(marker)
    }
    map.addLayer(markers);
  }
}

function parseQueryString() {
  return Object.fromEntries(
    document.location.search
      .replace(/^\?/, '')
      .split('&')
      .filter(pair => !!pair)
      .map(pair => pair.split('=').map(decodeURIComponent))
  )
}

async function fetchGroups(backend) {
  const url = `${backend}/api/v1/chapters.json`
  const res = await fetch(url, {})
  return await res.json();
}

window.onload = main;
