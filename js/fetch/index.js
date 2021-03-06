// Example of using API4AI Personal Protective Equipment detection - entrance.

// Use 'demo' mode just to try api4ai for free. Free demo is rate limited.
// For more details visit:
//   https://api4.ai
const MODE = 'demo'

const OPTIONS = {
  demo: {
    url: 'https://demo.api4ai.cloud/ppe-entrance/v1/results',
    headers: { 'A4A-CLIENT-APP-ID': 'sample' }
  }
}

document.addEventListener('DOMContentLoaded', function (event) {
  const input = document.getElementById('file')
  const raw = document.getElementById('raw')
  const sectionRaw = document.getElementById('sectionRaw')
  const parsed = document.getElementById('parsed')
  const sectionParsed = document.getElementById('sectionParsed')
  const spinner = document.getElementById('spinner')

  input.addEventListener('change', (event) => {
    const file = event.target.files[0]
    if (!file) {
      return false
    }

    sectionRaw.hidden = true
    sectionParsed.hidden = true
    spinner.hidden = false

    // Preapare request.
    const form = new FormData()
    form.append('image', file)
    const requestOptions = {
      method: 'POST',
      body: form,
      headers: OPTIONS[MODE].headers
    }

    // Make request.
    fetch(OPTIONS[MODE].url, requestOptions)
      .then(response => response.json())
      .then(function (response) {
        // Print raw response.
        raw.textContent = JSON.stringify(response, undefined, 2)
        sectionRaw.hidden = false
        // Parse response and print people count and detected equipment.
        const objects = response.results[0].entities[0].objects
          .map((obj) => obj.entities[1].classes)
        parsed.textContent = `Recognized persons: ${objects.length}\n\n`
        objects.forEach((obj, index) => {
          parsed.textContent += `Person ${index + 1} and probabilities:\n`
          for (const key in obj) {
            parsed.textContent += `\t${key}: ${obj[key]}\n`
          }
        })
        sectionParsed.hidden = false
      })
      .catch(function (error) {
        // Error can be handled here.
        console.error(error)
      })
      .then(function () {
        spinner.hidden = true
      })
  })
})
