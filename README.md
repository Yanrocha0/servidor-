# servidor- {
  "version": 2,
  "builds": [
    {
      "src": "api/proxy_api.php",
      "use": "@vercel/php"
    }
  ],
  "rewrites": [
    {
      "source": "/api/proxy",
      "destination": "/api/proxy_api.php"
    }
  ]
}
