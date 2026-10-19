// shared helper for talking to the backend
async function request(path, options = {}) {
  const res = await fetch(path, {
    headers: { 'Content-Type': 'application/json' },
    ...options,
  })

  // if the php server is down the proxy sends back html, not json, so fall back to an empty object instead of crashing
  const data = await res.json().catch(() => ({}))

  // anything that isn't a 2xx status becomes an error the components can show
  if (!res.ok) {
    throw new Error(data.error || 'Something went wrong, try again.')
  }

  return data
}

// get the most recent links from the backend
export function getLinks() {
  return request('/api/links')
}

// send a long url and get the short link back 
export function shortenUrl(url) {
  return request('/api/links', {
    method: 'POST',
    body: JSON.stringify({ url }),
  })
}