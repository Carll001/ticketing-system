<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table'
import { Badge } from '@/components/ui/badge'
import { Department, Task } from '@/types'
import { ArrowLeft } from 'lucide-vue-next'
import { getInitials } from '@/composables/useInitials';

// Props
const props = defineProps<{
  department: {
    data: Department
  }
  tasks?: Task[]
}>()


</script>

<template>

  <Head :title="`${department?.data.name} - Department`" />

  <AppLayout>
    <div class="flex flex-col flex-1 gap-4 p-4">

      <!-- Header -->
      <section class="flex items-center gap-4">
        <Link href="/department">
          <Button variant="ghost" size="sm" class="flex items-center gap-2">
            <ArrowLeft class="h-4 w-4" />
          </Button>
        </Link>
        <div class="w-md">
          <h1 class="text-2xl font-bold truncate">{{ department?.data.name }}</h1>
          <p class="text-sm text-muted-foreground">Department Details</p>
        </div>
      </section>

      <div class="flex gap-6">
        <!-- Left Card -->
        <aside class="w-80 flex-shrink-0">
          <Card class="w-80">
            <CardHeader class="items-center space-y-4 text-center">
              <div
                class="flex h-24 w-24 items-center justify-center rounded-full bg-gradient-to-br from-blue-400 to-blue-600 text-white mx-auto text-3xl font-bold">
                {{ getInitials(department?.data.name || '') }}
              </div>

              <CardTitle class="text-xl break-all">{{ department?.data.name }}</CardTitle>
              <p class="text-sm text-muted-foreground">Department</p>
            </CardHeader>

            <CardContent class="space-y-4 text-center">
              <div class="space-y-2">
                <h3 class="text-sm font-semibold">Department ID</h3>
                <p class="text-sm text-muted-foreground">{{ department?.data.id }}</p>
              </div>
            </CardContent>
          </Card>
        </aside>

        <!-- Right Section -->
        <div class="flex-1 space-y-6">

          <!-- USERS CARD -->
          <!-- USERS CARD -->
          <Card>
            <CardHeader>
              <CardTitle>Assigned Users</CardTitle>
            </CardHeader>

            <CardContent>
              <div v-if="department?.data.assigned_users?.length" class="overflow-x-auto">
                <Table>
                  <TableHeader>
                    <TableRow>
                      <TableHead>Name</TableHead>
                      <TableHead>Email</TableHead>
                    </TableRow>
                  </TableHeader>

                  <TableBody>
                    <TableRow v-for="user in department.data.assigned_users" :key="user.id">
                      <TableCell class="font-medium">{{ user.name }}</TableCell>
                      <TableCell>{{ user.email || '-' }}</TableCell>
                    </TableRow>
                  </TableBody>
                </Table>
              </div>

              <div v-else class="text-sm text-muted-foreground text-center">
                No users assigned to this department
              </div>
            </CardContent>
          </Card>


          <!-- TASKS CARD -->
          <div class="rounded-lg border shadow-sm">
            <div class="border-b p-6">
              <h3 class="text-lg font-semibold">Assigned Tasks</h3>
            </div>

            <div v-if="tasks?.length" class="overflow-x-auto">
              <Table>
                <TableHeader>
                  <TableRow>
                    <TableHead>Task Title</TableHead>
                    <TableHead>Due Date</TableHead>
                    <TableHead>Status</TableHead>
                  </TableRow>
                </TableHeader>

                <TableBody>
                  <TableRow v-for="task in tasks" :key="task.id">
                    <TableCell class="font-medium">{{ task.title }}</TableCell>
                    <TableCell>{{ task.due_date ? new Date(task.due_date).toLocaleDateString() : '-' }}</TableCell>
                    <TableCell>
                      <Badge variant="secondary">Assigned</Badge>
                    </TableCell>
                  </TableRow>
                </TableBody>
              </Table>
            </div>

            <div v-else class="p-6 text-center text-muted-foreground">
              <p>No tasks assigned to this department</p>
            </div>
          </div>

        </div>
      </div>
    </div>

    <pre>{{ props }}</pre>
  </AppLayout>
</template>
