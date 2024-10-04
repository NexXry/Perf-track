<script setup>
import {ref} from "vue";
import axios from "axios";

const props = defineProps({
  exercices: [],
  userExercices: []
})

const userExercicesList = ref(props.userExercices)

const addExercice = () => {
  userExercicesList.value.push("")
  console.log(userExercicesList.value)
}

const updateExercice = async (e) => {
  const exercice = e.target.value;
  await axios.post('/add/exercice', {
    name: exercice
  })
}

</script>

<template>
  <div class="flex flex-col justify-between gap-6 min-h-[650px]">
    <div>
      <label v-for="listExercice in userExercicesList" class="form-control w-full max-w-xs">
        <div class="label">
          <span class="label-text">Pick an exercice to track</span>
        </div>
        <select @change="updateExercice" class="select select-bordered">
          <option disabled :selected="listExercice === ''">Pick one</option>
          <option v-for="exercice in props.exercices" :selected="listExercice === exercice">{{ exercice }}</option>
        </select>
      </label>
    </div>
    <button @click="addExercice" class="btn btn-outline btn-primary mb-3">Add</button>
  </div>
</template>
