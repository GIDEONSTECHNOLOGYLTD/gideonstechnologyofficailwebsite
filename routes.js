const express = require('express');
const app = express();
const port = 3000;

// ...existing code...

// Add route for the gstore resource
app.get('/gstore', (req, res) => {
  res.status(200).json({
    success: true,
    message: 'GStore API endpoint',
    data: {
      items: [],
      version: '1.0.0'
    }
  });
});

// ...existing code...

app.listen(port, () => {
  console.log(`Server is running on port ${port}`);
});