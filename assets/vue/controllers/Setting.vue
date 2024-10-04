<script setup>
import {ref, watch} from "vue";
import axios from "axios";

const props = defineProps({
  exercices: [],
  objectiveLimit: Number
})

const objectiveCounter = ref(props.objectiveLimit)
const exercicesList = ref(props.exercices)
const newExercice = ref("")

const upObjective = async () => {
  objectiveCounter.value++
}

const lowerObjective = async () => {
  objectiveCounter.value--
}

const updateObjective = (e) => {
  objectiveCounter.value = e.target.value
}


const addExercice = async (e) => {
  if (newExercice.value !== "") {
    exercicesList.value.push(newExercice.value)
    newExercice.value = ""
  }


  await axios.post('/setting/add', new FormData(e.target))
}

const removeExercice = async (exercice) => {
  const exercices = exercicesList.value
  exercicesList.value = exercices.filter((ex) => {
    if (ex !== exercice) {
      return ex
    }
  })
  await axios.post('/setting/remove', {
    name: exercice
  })
}

watch(objectiveCounter, async (newObj) => {
  await axios.post('/setting/set/objective', {
    count: newObj
  })
})
</script>

<template>
  <div class="mt-6 flex flex-col gap-12 mx-10">
    <div>
      <h2 class="font-medium mb-1">Set your reps objective</h2>
      <label class="input input-bordered flex items-center gap-2">
        <i @click="lowerObjective" class="fa-solid fa-minus"></i>
        <input @input="updateObjective" type="number" class="grow text-center" placeholder="30"
               :value="objectiveCounter"/>
        <i @click="upObjective" class="fa-solid fa-plus"></i>
      </label>
    </div>
    <div class="flex flex-col">
      <h2 class="font-medium mb-1">Add a new exercice to exercices list</h2>
      <form @submit.prevent="addExercice" action="#" method="post" name="exercice">
        <input
            v-model="newExercice"
            type="text"
            id="exercice_name" name="exercice[name]"
            placeholder="Type here"
            class="input input-bordered input-primary w-full max-w-xs"/>
        <button type="submit" class="btn btn-outline btn-primary my-3">Add new exercice</button>
      </form>
    </div>
    <div class="flex flex-col">
      <h2 class="font-medium mb-1">Exercices list :</h2>
      <div class="flex flex-col gap-2 mb-3">
        <div v-for="exercice in exercicesList" :key="exercice" class="card bg-base-100 w-full shadow-xl">
          <div class="card-body flex flex-row">
            <p>{{ exercice }}</p>
            <button @click="removeExercice(exercice)" class="btn btn-square btn-sm hover:btn-error">
              <i class="fa-solid fa-trash"></i>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
