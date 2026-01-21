<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card'
import { Department } from '@/types'

const props = defineProps<{
  department: { data: Department },
  tasks: Array<{
    id: string,
    title: string,
    description: string | null,
    due_date: string | null
  }>
}>()

const formatDate = (date: string | null) => {
  if (!date) return ''
  const d = new Date(date)
  return new Intl.DateTimeFormat('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric'
  }).format(d)
}
</script>

<template>
  <Head title="View Department" />

  <AppLayout>
    <div class="max-w-7xl mx-auto p-6 space-y-6">

      <!-- Header -->
      <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Department Details</h1>

        <Link href="/department">
          <Button variant="outline">
            Back
          </Button>
        </Link>
      </div>

      <!-- Two-column layout -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Left Column: Department Info -->
        <div class="space-y-6 lg:col-span-1">
          <Card>
            <CardHeader>
              <CardTitle>Information</CardTitle>
              <CardDescription>General details about this department.</CardDescription>
            </CardHeader>

            <CardContent class="space-y-4">
              <div>
                <p class="text-sm text-muted-foreground">Department Name</p>
                <p class="text-lg font-medium">{{ props.department.data.name }}</p>
              </div>

              <div>
                <p class="text-sm text-muted-foreground">Department ID</p>
                <p class="text-lg font-medium">{{ props.department.data.id }}</p>
              </div>
            </CardContent>
          </Card>
        </div>

        <!-- Right Column: Tasks Assigned -->
        <div class="space-y-4 lg:col-span-2">
          <Card>
            <CardHeader>
              <CardTitle>Tasks Assigned</CardTitle>
              <CardDescription>A list of all active tasks for this department.</CardDescription>
            </CardHeader>

            <CardContent class="space-y-4">
              <div v-if="tasks.length === 0" class="text-center py-6 text-gray-500">
                No tasks assigned
              </div>

              <div v-else class="space-y-4">
                <Card v-for="task in tasks" :key="task.id" class="border">
                  <CardHeader>
                    <div class="flex items-center justify-between">
                      <CardTitle class="text-xl font-semibold">{{ task.title }}</CardTitle>
                      <!-- Display Due Date text, no badge -->
                      <p class="text-xs text-muted-foreground">
                        {{ task.due_date ? `Due Date: ${formatDate(task.due_date)}` : 'No Due Date' }}
                      </p>
                    </div>
                    <CardDescription v-if="task.description">
                      {{ task.description }}
                    </CardDescription>
                  </CardHeader>
                </Card>
              </div>
            </CardContent>
          </Card>
        </div>

      </div>
    </div>
  </AppLayout>
</template>
