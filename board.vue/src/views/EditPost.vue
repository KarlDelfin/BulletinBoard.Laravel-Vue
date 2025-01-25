<template>
  <label for="writer">Writer</label>
  <br />
  <input id="writer" v-model="form.writer" placeholder="Enter Writer" />
  <br />
  <label for="subject">Subject</label>
  <br />
  <input id="subject" v-model="form.subject" placeholder="Enter Subject" />
  <br />
  <label for="message">Message</label>
  <br />
  <textarea id="message" v-model="form.message" placeholder="Message"></textarea>
  <br />
  <input type="file" @change="onchangeFile" accept=".jpg, .png, .jpeg" />
  <br />
  <label for="File">File</label>
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
  <button @click="$router.push(`/post?postId=${postId}`)">Back</button>
  <button @click="submitForm">Submit</button>
</template>

<script>
import axios from 'axios'
const api = 'http://127.0.0.1:8000/api'
export default {
  data() {
    return {
      file: [],
      fileExtension: '',
      postId: '',
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
    onchangeFile(e) {
      this.file = e.target.files[0]
      this.fileExtension = e.target.files[0].name.split('.').pop().toLowerCase()
    },

    submitForm() {
      if (this.file.length == undefined) {
        const reader = new FileReader()
        reader.readAsDataURL(this.file)
        reader.onload = async () => {
          const encodedFile = reader.result.split(',')[1]
          const data = {
            writer: this.form.writer,
            subject: this.form.subject,
            message: this.form.message,
            file: encodedFile,
            fileExtension: this.fileExtension,
          }
          axios.put(`${api}/Post/${this.postId}`, data).then(() => {
            this.$router.push(`/post?postId=${this.postId}`)
            this.getPostByPostId()
          })
        }
      }

      if (this.file.length == 0) {
        const data = {
          writer: this.form.writer,
          subject: this.form.subject,
          message: this.form.message,
          file: '',
        }
        axios.put(`${api}/Post/${this.postId}`, data).then(() => {
          this.$router.push(`/post?postId=${this.postId}`)
          this.getPostByPostId()
        })
      }
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
