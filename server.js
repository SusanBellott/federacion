const express = require('express');
const path = require('path');
const app = express();

// Redirigir todas las solicitudes a public/index.php
app.use(express.static(path.join(__dirname, 'public')));

// Catch-all para manejar rutas desconocidas y redirigirlas a index.php
app.get('*', (req, res) => {
  res.sendFile(path.join(__dirname, 'public', 'index.php'));
});

const port = process.env.PORT || 3000;
app.listen(port, () => {
  console.log(`Servidor iniciado en puerto ${port}`);
});
