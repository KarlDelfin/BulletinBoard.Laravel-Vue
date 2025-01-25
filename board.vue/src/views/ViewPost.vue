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
  <img v-if="isValidImage(form.file) == 'image'" :src="form.file" height="200" width="200" />
  <video v-else-if="isValidImage(form.file) == 'video'" width="400" controls>
    <source :src="form.file" type="video/mp4" />
    <source :src="form.file" type="video/ogg" />
    Your browser does not support HTML video.
  </video>
  <a v-else :href="form.file" download>
    Download file
    <br />
    <img src="../assets/document.png" height="100" width="100"
  /></a>

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
    isValidImage(src) {
      const base64Pattern = /^data:image\/(png|jpg|jpeg|gif);base64,/
      if (base64Pattern.test(src)) {
        return 'image'
      }

      const videoPattern = /^data:video\/mp4;base64,/
      if (videoPattern.test(src)) {
        return 'video'
      }

      const image = new Image()
      image.src = src
      return image.complete && image.height !== 0
    },
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
