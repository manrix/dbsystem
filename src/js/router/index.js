import Vue from 'vue'
import Router from 'vue-router'
import Backups from '../pages/backups/Index.vue'
import Tasks from '../pages/tasks/Index.vue'
import Database from '../pages/databases/Database.vue'
import Databases from '../pages/databases/Index.vue'
import Destination from '../pages/destinations/Destination.vue'
import Destinations from '../pages/destinations/Index.vue'
import User from '../pages/users/User.vue'
import Users from '../pages/users/Index.vue'
import Profile from '../pages/users/Profile.vue'
import TaskEdit from '../pages/tasks/Edit.vue'
import TaskShow from '../pages/tasks/Show.vue'
import Task from '../pages/tasks/Task.vue'

Vue.use(Router);

export default new Router({
    linkActiveClass: 'is-active',
    routes: [
        {
            path: '/backups',
            name: 'backups',
            component: Backups,
            meta: {
                title: 'Backups',
                breadcrumb: 'Backups'
            }
        },
        {
            path: '/tasks',
            component: Task,
            meta: {
                breadcrumb: 'Tasks'
            },
            children: [
                {
                    path: '',
                    name: 'tasks',
                    component: Tasks,
                    meta: {
                        title: 'Tasks',
                    }
                },
                {
                    path: 'new',
                    name: 'new_task',
                    component: TaskEdit,
                    meta: {
                        title: 'New Task',
                        breadcrumb: 'New',
                    }
                },
                {
                    path: ':id/edit',
                    name: 'edit_task',
                    component: TaskEdit,
                    props: true,
                    meta: {
                        title: 'Edit Task',
                        breadcrumb: 'Edit',
                    }
                },
                {
                    path: ':id/overview',
                    name: 'show_task',
                    component: TaskShow,
                    props: true,
                    meta: {
                        title: 'Task Overview',
                        breadcrumb: 'Overview',
                    }
                },
            ]
        },
        {
            path: '/databases',
            component: Database,
            meta: {
                breadcrumb: 'Databases',
            },
            children: [
                {
                    path: '',
                    name: 'databases',
                    component: Databases,
                    meta: {
                        title: 'Databases',
                    }
                }
            ]
        },
        {
            path: '/destinations',
            component: Destination,
            meta: {
                breadcrumb: 'Destinations'
            },
            children: [
                {
                    path: '',
                    name: 'destinations',
                    component: Destinations,
                    meta: {
                        title: 'Destinations',
                    }
                }
            ]
        },
        {
            path: '/users',
            component: User,
            meta: {
                breadcrumb: 'Users',
            },
            children: [
                {
                    path: '',
                    name: 'users',
                    component: Users,
                    meta: {
                        title: 'Users',
                    }
                },
                {
                    path: ':id/profile',
                    name: 'user_profile',
                    component: Profile,
                    props: true,
                    meta: {
                        title: 'User Profile',
                        breadcrumb: 'Profile',
                    }
                },
            ]
        },
        {
            path: '/',
            redirect: '/backups',
        }
    ]
})
