<!-- SearchableSelect.vue -->
<template>
    <div class="searchable-select-container">
        <div
            class="select-field"
            :class="{ active: isOpen }"
            @click="toggleDropdown"
        >
            <input
                type="text"
                v-model="searchQuery"
                @click.stop
                @input="filterOptions"
                @focus="isOpen = true"
                :placeholder="placeholder"
                class="bg-transparent text-slate-800 text-sm block w-full rounded-lg border-0 px-3 py-2 placeholder:text-slate-500 outline-none transition-all pr-10"
            />

            <div class="select-arrow">
                <svg
                    class="text-red-600"
                    width="16"
                    height="16"
                    viewBox="0 0 24 24"
                    fill="none"
                    :class="{ rotate: isOpen }"
                >
                    <path
                        d="M6 9L12 15L18 9"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    />
                </svg>
            </div>
        </div>

        <!-- Dropdown menu -->
        <div v-if="isOpen" class="select-dropdown">
            <div v-if="filteredOptions.length === 0" class="no-results">
                No se encontraron resultados
            </div>
            <div
                v-for="option in filteredOptions"
                :key="option.value"
                @click="selectOption(option)"
                class="dropdown-option"
                :class="{ selected: selectedItem === option.value }"
            >
                {{ option.label }}
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from "vue";

const props = defineProps({
    modelValue: {
        type: [String, Number],
        default: "",
    },
    options: {
        type: Array,
        required: true,
    },
    placeholder: {
        type: String,
        default: "Seleccione una opción",
    },
    fieldType: {
        type: String,
        default: "default", // Puede ser: 'distrito', 'codigoSie', 'institucion', 'default'
    },
});

const emit = defineEmits(["update:modelValue"]);

const isOpen = ref(false);
const searchQuery = ref("");
const selectedItem = ref(props.modelValue);
const sortedOptions = computed(() => {
    if (!Array.isArray(props.options)) return [];
    return [...props.options].sort((a, b) => {
        const labelA = a?.label?.toString() || "";
        const labelB = b?.label?.toString() || "";
        return labelA.localeCompare(labelB);
    });
});

const filteredOptions = computed(() => {
    if (!searchQuery.value) return sortedOptions.value;
    return sortedOptions.value.filter((option) => {
        const label = option?.label || "";
        return label.toLowerCase().includes(searchQuery.value.toLowerCase());
    });
});

// Update the display value when modelValue changes
watch(
    () => props.modelValue,
    () => {
        if (!props.modelValue) {
            searchQuery.value = "";
            selectedItem.value = null;
            return;
        }

        selectedItem.value = props.modelValue;
        const selectedOption = props.options.find(
            (option) => option.value == props.modelValue
        );
        if (selectedOption) {
            searchQuery.value = selectedOption.label;
        }
    },
    { immediate: true }
);

const toggleDropdown = () => {
    isOpen.value = !isOpen.value;
};

const selectOption = (option) => {
    selectedItem.value = option.value;
    emit("update:modelValue", option.value);
    searchQuery.value = option.label;
    isOpen.value = false;
};

const filterOptions = () => {
    isOpen.value = true;
};

// Close dropdown when clicking outside
const closeOnClickOutside = (e) => {
    if (!e.target.closest(".searchable-select-container")) {
        isOpen.value = false;
    }
};

onMounted(() => {
    document.addEventListener("click", closeOnClickOutside);
});

onUnmounted(() => {
    document.removeEventListener("click", closeOnClickOutside);
});
</script>

<style scoped>
.searchable-select-container {
    position: relative;
    width: 100%;
}

/* Campo principal: rojo translúcido + blur + borde rojo */
.select-field {
    display: flex;
    align-items: center;
    justify-content: space-between;
    border: 1px solid rgba(220, 38, 38, 0.5); /* red-600/50 */
    border-radius: 0.5rem; /* rounded-lg grande */
    background: #ffffff; /* red-600 ~ /10 */
    backdrop-filter: blur(6px);
    -webkit-backdrop-filter: blur(6px);
    transition: all 0.2s ease;
    padding-right: 0.75rem;
    box-shadow: 0 1px 2px rgba(16, 24, 40, 0.05);
}

/* Estado abierto/focus: anillo rojo */
.select-field.active {
    border-color: #dc2626; /* focus:border-red-600 */
    box-shadow: 0 0 0 2px rgba(220, 38, 38, 0.3); /* focus:ring-red-500/30 */
}

.select-field input {
    width: 100%;
    border: none;
    outline: none;
    padding: 0.5rem 0.75rem;
    font-size: 0.875rem;
    border-radius: 0.5rem;
    color: #0f172a; /* slate-900 */
}
.select-field input::placeholder {
    color: #64748b; /* slate-400 */
}

/* Flecha con separación sutil y tono rojo */
.select-arrow {
    height: 100%;
    padding: 0 0.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    border-left: 1px solid #e5e7eb;
    background-color: transparent;
    border-top-right-radius: 0.75rem;
    border-bottom-right-radius: 0.75rem;
}
.select-arrow svg {
    transition: transform 0.2s ease;
}
.select-arrow svg.rotate {
    transform: rotate(180deg);
}

/* Dropdown claro con borde rojo */
.select-dropdown {
    position: absolute;
    z-index: 50;
    top: calc(100% + 0.25rem);
    left: 0;
    right: 0;
    border: 1px solid rgba(220, 38, 38, 0.4); /* red-600/40 */
    border-radius: 0.75rem;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    max-height: 15rem;
    overflow-y: auto;
    overflow-x: hidden;
    width: 100%;
    background-color: #ffffff; /* blanco */
    color: #0f172a; /* slate-900 */
}

/* Opciones */
.dropdown-option {
    padding: 0.6rem 1rem;
    font-size: 0.875rem;
    color: #0f172a;
    cursor: pointer;
    transition: background-color 0.15s ease;
    background-color: #ffffff;
}
.dropdown-option:hover {
    background-color: #fef2f2; /* red-50 */
}
.dropdown-option.selected {
    background-color: #fee2e2; /* red-100 */
}

/* Sin resultados */
.no-results {
    padding: 0.75rem 1rem;
    color: #94a3b8; /* slate-400/500 */
    font-size: 0.875rem;
    text-align: center;
}

/* Scrollbar del dropdown */
.select-dropdown::-webkit-scrollbar {
    width: 6px;
}
.select-dropdown::-webkit-scrollbar-track {
    background: transparent;
}
.select-dropdown::-webkit-scrollbar-thumb {
    background: #cbd5e0;
    border-radius: 3px;
}
</style>
