<template>
    <defaultLayout v-if="layout == 'default'"></defaultLayout>
    <layoutComponents v-if="layout == 'layout1'"></layoutComponents>
</template>
<script lang="ts" setup>
import defaultLayout from "@/components/layouts/default/Layout.vue";
import layoutComponents from "@/components/layouts/layout1/Layout.vue";
import { useAppStore } from "@/store";
import { storeToRefs } from "pinia";

const { layout } = storeToRefs(useAppStore());

</script>