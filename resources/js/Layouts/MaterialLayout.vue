<script setup>
import { AppBar, Toolbar, Typography, Container } from "@mui/material";
</script>

<template>
  <div>
    <AppBar position="static">
      <Toolbar>
        <Typography variant="h6">Mi Dashboard</Typography>
      </Toolbar>
    </AppBar>

    <Container>
      <slot />
      <!-- Aquí se renderiza la página -->
    </Container>
  </div>
</template>
