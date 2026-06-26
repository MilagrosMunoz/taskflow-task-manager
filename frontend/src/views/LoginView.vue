<template>
  <div
    class="container d-flex justify-content-center align-items-center min-vh-100"
  >
    <div
      class="card shadow-lg p-4"
      style="max-width: 450px; width: 100%; border-radius: 15px"
    >
      <div class="text-center mb-4">
        <h1 class="h3 mb-1 fw-bold text-primary">
          <i class="bi bi-check2-square"></i> TaskFlow
        </h1>
        <p class="text-muted small">Gestiona tus tareas de forma eficiente</p>
      </div>

      <ul
        class="nav nav-pills nav-justified mb-4"
        id="pills-tab"
        role="tablist"
      >
        <li class="nav-item">
          <button
            class="nav-link"
            :class="{ active: isLogin }"
            @click="
              isLogin = true;
              clearForm();
            "
          >
            Ingresar
          </button>
        </li>
        <li class="nav-item">
          <button
            class="nav-link"
            :class="{ active: !isLogin }"
            @click="
              isLogin = false;
              clearForm();
            "
          >
            Registrarse
          </button>
        </li>
      </ul>

      <div
        v-if="errorMessage"
        class="alert alert-danger py-2 small"
        role="alert"
      >
        <i class="bi bi-exclamation-triangle-fill"></i> {{ errorMessage }}
      </div>
      <div
        v-if="successMessage"
        class="alert alert-success py-2 small"
        role="alert"
      >
        <i class="bi bi-check-circle-fill"></i> {{ successMessage }}
      </div>

      <form v-if="isLogin" @submit.prevent="handleLogin">
        <div class="mb-3">
          <label class="form-label small fw-bold">Correo electrónico</label>
          <div class="input-group">
            <span class="input-group-text bg-white"
              ><i class="bi bi-envelope text-muted"></i
            ></span>
            <input
              type="email"
              v-model="loginForm.email"
              class="form-control"
              placeholder="ejemplo@correo.com"
              required
            />
          </div>
        </div>

        <div class="mb-4">
          <label class="form-label small fw-bold">Contraseña</label>
          <div class="input-group">
            <span class="input-group-text bg-white"
              ><i class="bi bi-lock text-muted"></i
            ></span>
            <input
              type="password"
              v-model="loginForm.password"
              class="form-control"
              placeholder="******"
              required
            />
          </div>
        </div>

        <button
          type="submit"
          class="btn btn-primary w-100 fw-bold py-2"
          :disabled="loading"
        >
          <span
            v-if="loading"
            class="spinner-border spinner-border-sm me-1"
          ></span>
          {{ loading ? "Ingresando..." : "Iniciar Sesión" }}
        </button>
      </form>

      <form v-else @submit.prevent="handleRegister">
        <div class="mb-3">
          <label class="form-label small fw-bold">Nombre Completo</label>
          <div class="input-group">
            <span class="input-group-text bg-white"
              ><i class="bi bi-person text-muted"></i
            ></span>
            <input
              type="text"
              v-model="registerForm.name"
              class="form-control"
              placeholder="Juan Pérez"
              required
            />
          </div>
        </div>

        <div class="mb-3">
          <label class="form-label small fw-bold">Correo electrónico</label>
          <div class="input-group">
            <span class="input-group-text bg-white"
              ><i class="bi bi-envelope text-muted"></i
            ></span>
            <input
              type="email"
              v-model="registerForm.email"
              class="form-control"
              placeholder="ejemplo@correo.com"
              required
            />
          </div>
        </div>

        <div class="mb-3">
          <label class="form-label small fw-bold">Contraseña</label>
          <div class="input-group">
            <span class="input-group-text bg-white"
              ><i class="bi bi-lock text-muted"></i
            ></span>
            <input
              type="password"
              v-model="registerForm.password"
              class="form-control"
              placeholder="Mínimo 6 caracteres"
              required
            />
          </div>
        </div>

        <div class="mb-4">
          <label class="form-label small fw-bold">Confirmar Contraseña</label>
          <div class="input-group">
            <span class="input-group-text bg-white"
              ><i class="bi bi-lock-fill text-muted"></i
            ></span>
            <input
              type="password"
              v-model="registerForm.password_confirmation"
              class="form-control"
              placeholder="Repite tu contraseña"
              required
            />
          </div>
        </div>

        <button
          type="submit"
          class="btn btn-success w-100 fw-bold py-2"
          :disabled="loading"
        >
          <span
            v-if="loading"
            class="spinner-border spinner-border-sm me-1"
          ></span>
          {{ loading ? "Registrando..." : "Crear Cuenta" }}
        </button>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue";
import { useRouter } from "vue-router";
import axiosClient from "../axios"; // Conexión a nuestra API configurada

const router = useRouter();

// Estados reactivos
const isLogin = ref(true);
const loading = ref(false);
const errorMessage = ref("");
const successMessage = ref("");

// Modelos de los formularios
const loginForm = ref({
  email: "",
  password: "",
});

const registerForm = ref({
  name: "",
  email: "",
  password: "",
  password_confirmation: "",
});

// Limpiar mensajes y formularios al cambiar de pestaña
const clearForm = () => {
  errorMessage.value = "";
  successMessage.value = "";
  loginForm.value = { email: "", password: "" };
  registerForm.value = {
    name: "",
    email: "",
    password: "",
    password_confirmation: "",
  };
};

// Lógica de Iniciar Sesión
const handleLogin = async () => {
  loading.value = true;
  errorMessage.value = "";
  try {
    const response = await axiosClient.post("/login", loginForm.value);

    // Guardamos el Token y el usuario en el navegador
    localStorage.setItem("TOKEN", response.data.token);
    localStorage.setItem("USER_NAME", response.data.user.name);

    // Redireccionar automáticamente al Dashboard de Tareas
    router.push({ name: "dashboard" });
  } catch (error) {
    errorMessage.value =
      error.response?.data?.error ||
      "Error al iniciar sesión. Verifica tus datos.";
  } finally {
    loading.value = false;
  }
};

// Lógica de Registro de Usuario
const handleRegister = async () => {
  loading.value = true;
  errorMessage.value = "";
  successMessage.value = "";

  if (
    registerForm.value.password !== registerForm.value.password_confirmation
  ) {
    errorMessage.value = "Las contraseñas no coinciden.";
    loading.value = false;
    return;
  }

  try {
    const response = await axiosClient.post("/register", registerForm.value);
    successMessage.value = "¡Registro exitoso! Iniciando sesión...";

    // Guardamos las credenciales directamente para loguearlo de inmediato
    localStorage.setItem("TOKEN", response.data.token);
    localStorage.setItem("USER_NAME", response.data.user.name);

    setTimeout(() => {
      router.push({ name: "dashboard" });
    }, 1500);
  } catch (error) {
    // Si la API devuelve errores de validación (ej: email ya tomado) los atrapamos aquí
    const errors = error.response?.data;
    if (typeof errors === "object") {
      errorMessage.value = Object.values(errors).flat().join(" ");
    } else {
      errorMessage.value = "Ocurrió un error en el registro.";
    }
  } finally {
    loading.value = false;
  }
};
</script>
