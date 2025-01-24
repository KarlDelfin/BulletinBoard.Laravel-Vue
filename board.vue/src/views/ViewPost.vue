<template>
  <b>Writer</b>
  <p>{{ form.writer }}</p>
  <br />
  <b>Subject</b>
  <p>{{ form.subject }}</p>
  <br />
  <b>Message</b>
  <p>{{ form.message }}</p>
  <br />
  <b>File</b>
  <br />
  <img :src="form.file" height="200" width="200" />

  <br />
  <button @click="$router.push('/')">Back</button>
  <button @click="$router.push(`/edit-post?postId=${postId}`)">Edit</button>
</template>

<script>
import axios from 'axios'
const api = 'http://127.0.0.1:8000/api'
export default {
  data() {
    return {
      postId: '',
      posts: [],
      form: {
        writer: '',
        subject: '',
        message: '',
        file: '',
      },
    }
  },

  methods: {
    getPostByPostId() {
      axios.get(`${api}/Post/${this.postId}`).then((response) => {
        this.form.writer = response.data.writer
        this.form.subject = response.data.subject
        this.form.message = response.data.message
        this.form.file = response.data.file
      })
    },
  },
  mounted() {
    const url = new URLSearchParams(window.location.search)
    const postId = url.get('postId')
    this.postId = postId
    this.getPostByPostId()
  },
}
</script>
