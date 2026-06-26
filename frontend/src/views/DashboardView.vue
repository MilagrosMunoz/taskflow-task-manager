<template>
  <div>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm mb-4">
      <div class="container">
        <span class="navbar-brand fw-bold">
          <i class="bi bi-check2-square text-primary"></i> TaskFlow Dashboard
        </span>
        <div class="d-flex align-items-center">
          <span class="text-light me-3 small">
            <i class="bi bi-person-circle"></i> ¡Hola, {{ userName }}!
          </span>
          <button
            @click="handleLogout"
            class="btn btn-outline-danger btn-sm fw-bold"
          >
            <i class="bi bi-box-arrow-right"></i> Salir
          </button>
        </div>
      </div>
    </nav>

    <div class="container">
      <div class="row">
        <div class="col-md-4 mb-4">
          <div class="card shadow-sm border-0 p-4">
            <h5 class="fw-bold mb-3 text-secondary">Nueva Tarea</h5>

            <form @submit.prevent="createTask">
              <div class="mb-3">
                <label class="form-label small fw-bold">Título</label>
                <input
                  type="text"
                  v-model="newTask.title"
                  class="form-control"
                  placeholder="Ej: Estudiar Vue 3"
                  required
                />
              </div>

              <div class="mb-3">
                <label class="form-label small fw-bold">Descripción</label>
                <textarea
                  v-model="newTask.description"
                  class="form-control"
                  rows="3"
                  placeholder="Detalles de la tarea..."
                ></textarea>
              </div>

              <div class="mb-4">
                <label class="form-label small fw-bold">Prioridad</label>
                <select v-model="newTask.priority" class="form-select">
                  <option value="low">🟢 Baja</option>
                  <option value="medium">🟡 Media</option>
                  <option value="high">🔴 Alta</option>
                </select>
              </div>

              <button
                type="submit"
                class="btn btn-primary w-100 fw-bold"
                :disabled="loadingCreate"
              >
                <span
                  v-if="loadingCreate"
                  class="spinner-border spinner-border-sm me-1"
                ></span>
                <i class="bi bi-plus-circle-fill"></i> Agregar Tarea
              </button>
            </form>
          </div>
        </div>

        <div class="col-md-8">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold text-secondary mb-0">Mis Tareas</h5>
            <span class="badge bg-secondary">{{ tasks.length }} en total</span>
          </div>

          <div v-if="loadingFetch" class="text-center py-5">
            <div class="spinner-border text-primary" role="status"></div>
            <p class="text-muted small mt-2">Cargando tus tareas de Neon...</p>
          </div>

          <div
            v-else-if="tasks.length === 0"
            class="card p-5 text-center shadow-sm border-0 bg-white"
          >
            <i class="bi bi-clipboard-x display-4 text-muted mb-2"></i>
            <p class="fw-bold text-muted mb-0">
              No tienes tareas registradas todavía.
            </p>
            <small class="text-muted"
              >¡Usa el formulario de la izquierda para empezar!</small
            >
          </div>

          <div v-else class="row">
            <div class="col-12 mb-3" v-for="task in tasks" :key="task.id">
              <div
                class="card border-start border-4 shadow-sm"
                :class="getPriorityClass(task.priority).border"
              >
                <div class="card-body">
                  <div class="d-flex justify-content-between align-items-start">
                    <div>
                      <h5 class="card-title fw-bold mb-1">{{ task.title }}</h5>
                      <p class="card-text text-muted small mb-2">
                        {{ task.description }}
                      </p>

                      <div class="d-flex gap-2">
                        <span
                          style="cursor: pointer"
                          @click="toggleTaskCompletion(task)"
                          :class="
                            task.completed
                              ? 'badge bg-success'
                              : 'badge bg-secondary'
                          "
                        >
                          {{ task.completed ? "Completada" : "Pendiente" }}
                        </span>

                        <span
                          class="badge"
                          :class="getPriorityClass(task.priority).badge"
                        >
                          Prioridad: {{ getPriorityClass(task.priority).text }}
                        </span>
                      </div>
                    </div>

                    <button
                      @click="deleteTask(task.id)"
                      class="btn btn-outline-danger btn-sm border-0"
                    >
                      <i class="bi bi-trash-fill"></i>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRouter } from "vue-router";
import axiosClient from "../axios";

const router = useRouter();

const userName = ref(localStorage.getItem("USER_NAME") || "Usuario");
const tasks = ref([]);
const loadingFetch = ref(false);
const loadingCreate = ref(false);

const newTask = ref({
  title: "",
  description: "",
  priority: "medium", // Cambiado a 'medium' por defecto para que coincida con la DB
});

// Función optimizada para retornar bordes, badges y textos traducidos
const getPriorityClass = (priority) => {
  switch (priority) {
    case "high":
      return {
        border: "border-danger",
        badge: "bg-danger text-white",
        text: "Alta",
      };
    case "medium":
      return {
        border: "border-warning",
        badge: "bg-warning text-dark",
        text: "Media",
      };
    case "low":
    default:
      return {
        border: "border-success",
        badge: "bg-success text-white",
        text: "Baja",
      };
  }
};

const fetchTasks = async () => {
  loadingFetch.value = true;
  try {
    const response = await axiosClient.get("/tasks");
    tasks.value = response.data;
  } catch (error) {
    console.error("Error al traer las tareas:", error);
  } finally {
    loadingFetch.value = false;
  }
};

const createTask = async () => {
  loadingCreate.value = true;
  try {
    const response = await axiosClient.post("/tasks", newTask.value);
    tasks.value.unshift(response.data);
    newTask.value = { title: "", description: "", priority: "medium" };
  } catch (error) {
    console.error("Error al crear la tarea:", error);
  } finally {
    loadingCreate.value = false;
  }
};

const toggleTaskCompletion = async (task) => {
  try {
    const updatedStatus = !task.completed;
    await axiosClient.put(`/tasks/${task.id}`, { completed: updatedStatus });
    task.completed = updatedStatus;
  } catch (error) {
    console.error("Error al actualizar la tarea:", error);
  }
};

const deleteTask = async (id) => {
  if (!confirm("¿Estás seguro de que deseas eliminar esta tarea?")) return;
  try {
    await axiosClient.delete(`/tasks/${id}`);
    tasks.value = tasks.value.filter((task) => task.id !== id);
  } catch (error) {
    console.error("Error al borrar la tarea:", error);
  }
};

const handleLogout = () => {
  localStorage.removeItem("TOKEN");
  localStorage.removeItem("USER_NAME");
  router.push({ name: "login" });
};

onMounted(() => {
  fetchTasks();
});
</script>

<style scoped>
.card {
  transition:
    transform 0.2s ease,
    box-shadow 0.2s ease;
}
.card:hover {
  transform: translateY(-2px);
}
</style>
