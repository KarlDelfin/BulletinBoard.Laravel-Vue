<template>
  <select v-model="select">
    <option value="writer">Writer</option>
    <option value="subject">Subject</option>
  </select>
  <form @submit.prevent="getPost">
    <input v-model="search" />
    <button type="submit">Search</button>
  </form>
  <div>Total Elements: {{ pagination.totalElements }}</div>
  <table>
    <tr>
      <th>No.</th>
      <th>Subject</th>
      <th>Writer</th>
      <th>Date/Time Created</th>
    </tr>
    <tr v-for="post in posts" :key="post.id">
      <td>{{ post.id }}</td>
      <td>
        <input type="checkbox" @click="handleCheckedPost(post.id)" />
        <a :href="`/post?postId=${post.id}`">{{ post.subject }}</a>
      </td>
      <td>{{ post.writer }}</td>
      <td>{{ post.date_time_created }}</td>
    </tr>
  </table>
  <el-pagination
    layout="prev, pager, next"
    v-model:current-page="pagination.currentPage"
    v-model:page-size="pagination.elementsPerPage"
    :total="pagination.totalElements"
    @size-change="getPost"
    @current-change="getPost"
  />
  <button @click="deleteMultiplePost" :disabled="checkedPost.length == 0">Delete</button>
  <button @click="$router.push('/create-post')">Create</button>
</template>

<script>
import axios from 'axios'
const api = 'http://127.0.0.1:8000/api'
export default {
  data() {
    return {
      select: 'subject',
      search: '',
      pagination: {
        currentPage: 1,
        elementsPerPage: 10,
        totalElements: 0,
      },
      checkedPost: [],
      posts: [],
    }
  },

  methods: {
    handleCheckedPost(e) {
      const index = this.checkedPost.indexOf(e)
      if (index === -1) {
        this.checkedPost.push(e)
      } else {
        this.checkedPost.splice(index, 1)
      }
    },

    deleteMultiplePost() {
      if (this.checkedPost.length == 0) {
        alert('Please select posts to delete')
        return
      }

      const data = {
        ids: this.checkedPost,
      }

      axios
        .delete(`${api}/Post`, { data })
        .then(() => {
          console.log('success')
          this.getPost()
        })
        .catch((error) => {
          console.error('Error deleting posts:', error)
        })
    },

    getPost() {
      axios
        .get(
          `${api}/Post?currentPage=${this.pagination.currentPage}&elementsPerPage=${this.pagination.elementsPerPage}&search=${this.search}&select=${this.select}`,
        )
        .then((response) => {
          this.posts = response.data.results
          this.pagination.totalElements = response.data.totalElements
        })
    },
  },
  mounted() {
    this.getPost()
  },
}
</script>
