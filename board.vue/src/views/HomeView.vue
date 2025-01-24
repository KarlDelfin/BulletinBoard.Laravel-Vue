<template>
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
  <button @click="deleteMultiplePost">Delete</button>
  <button @click="$router.push('/create-post')">Create</button>
</template>

<script>
import axios from 'axios'
const api = 'http://127.0.0.1:8000/api'
export default {
  data() {
    return {
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
        ids: this.checkedPost, // Make sure 'checkedPost' contains an array of IDs
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
      axios.get(`${api}/Post`).then((response) => {
        this.posts = response.data
      })
    },
  },
  mounted() {
    this.getPost()
  },
}
</script>
