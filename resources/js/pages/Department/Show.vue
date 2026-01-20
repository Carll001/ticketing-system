<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { ArrowLeft, Building } from 'lucide-vue-next'
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
</script>

<template>
  <Head title="View Department" />

 <AppLayout>
    <div class="max-w-7xl mx-auto p-6 space-y-6">

      <!-- Header -->
     <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Department Details</h1>

        <Link href="/department">
          <Button variant="outline" class="gap-2">
            <ArrowLeft class="w-4 h-4" />
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
            </CardHeader>

            <CardContent class="space-y-4">
              <div v-if="tasks.length === 0" class="text-center py-6 text-gray-500">
                No tasks assigned
              </div>

              <div v-else class="space-y-4">
                <Card v-for="task in tasks" :key="task.id" class="border">
                  <CardHeader class="flex justify-between items-start">
                    <div class="flex items-center gap-2">
                      <FileText class="w-5 h-5 text-primary" />
                      <CardTitle class="text-lg font-medium">{{ task.title }}</CardTitle>
                    </div>
                    <span class="text-sm text-muted-foreground">
                      <Calendar class="w-4 h-4 inline mr-1" />
                      {{ task.due_date ?? 'No due date' }}
                    </span>
                  </CardHeader>
                  <CardContent>
                    <p>{{ task.description ?? '-' }}</p>
                  </CardContent>
                </Card>
              </div>
            </CardContent>
          </Card>
        </div>

      </div>
    </div>
  </AppLayout>
</template>
