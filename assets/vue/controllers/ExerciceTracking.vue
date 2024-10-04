<script setup>
import InputTracking from "./components/InputTracking.vue";

const props = defineProps({
  exercices: String,
  trackings: Array,
})

const getRepsByExercice = (ex) => {
  const track = props.trackings.filter((track) => {
    if (track.exercice === ex) {
      return track.reps
    }
  })
  return track[0]?.reps
}
</script>

<template>
  <div v-for="exercice in exercices" class="mt-6 flex flex-col gap-1">
    <h2 class="text-2xl font-medium">{{ exercice }}</h2>
    <InputTracking :exercice="exercice" :reps="getRepsByExercice(exercice)"></InputTracking>
  </div>
</template>
