import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import ViewPost from '../views/ViewPost.vue'
import CreatePost from '../views/CreatePost.vue'
import EditPost from '../views/EditPost.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: HomeView,
    },
    {
      path: '/post',
      name: 'post',
      component: ViewPost,
    },
    {
      path: '/create-post',
      name: 'createPost',
      component: CreatePost,
    },
    {
      path: '/edit-post',
      name: 'editPost',
      component: EditPost,
    },
  ],
})

export default router
