<script setup>
import {ref, watch} from "vue";
import axios from "axios";

const props = defineProps({
  exercice: String,
  reps: Number | null,
})

const objectiveCounter = ref(props.reps ?? 0)

const upObjective = () => {
  objectiveCounter.value++
}

const lowerObjective = () => {
  if (objectiveCounter.value > 0) {
    objectiveCounter.value--
  }
}
const updateObjective = (e) => {
  objectiveCounter.value = e.target.value
}

watch(objectiveCounter, async (newObjective, oldObjective) => {
  await axios.post("/exercices/set", {
    name: props.exercice,
    count: newObjective
  })
})

</script>

<template>
  <label class="input input-bordered flex items-center gap-2">
    <i @click="lowerObjective" class="fa-solid fa-minus"></i>
    <input @input="updateObjective" type="number" class="grow text-center" placeholder="0" :value="objectiveCounter"/>
    <i @click="upObjective" class="fa-solid fa-plus"></i>
  </label>
</template>
