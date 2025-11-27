<template>
    <main4></main4>
    <main5></main5>
    <commonFooter></commonFooter>
</template>
<script setup lang="ts">
import { getCurrentInstance, ref, onMounted, watch } from "vue";
import { storeToRefs } from "pinia";
import { useAppStore } from "@/store";
import main3 from "@/views/common/main-3.vue"
import main4 from "@/views/common/main-4.vue"
import main5 from "@/views/common/main-5.vue"
import commonFooter from "@/views/common/common-footer.vue"

</script>