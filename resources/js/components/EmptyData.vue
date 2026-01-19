<script setup lang="ts">
import {
    Empty,
    EmptyDescription,
    EmptyHeader,
    EmptyMedia,
    EmptyTitle,
} from '@/components/ui/empty';
import type { Component } from 'vue';

const empty = defineProps<{
    icon?: Component;
    title: string;
    message: string;
    length: boolean;
}>();
</script>
<template>
    <Empty v-show="length">
        <EmptyHeader>
            <EmptyMedia variant="icon">
                <component v-if="empty.icon" :is="empty.icon" />
            </EmptyMedia>
            <EmptyTitle class="capitalize">{{empty.title}}</EmptyTitle>
            <EmptyDescription>
                <p  class="capitalize">{{ empty.message }}</p>
                
            </EmptyDescription>
        </EmptyHeader>
    </Empty>
</template>
