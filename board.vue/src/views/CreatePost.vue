<template>
  <form @submit.prevent="submitForm">
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
    <button type="button" @click="$router.push('/')">Home</button>
    <button type="submit">Submit</button>
  </form>
</template>

<script>
import axios from 'axios'
const api = 'http://127.0.0.1:8000/api'
export default {
  data() {
    return {
      file: [],
      fileExtension: '',
      form: {
        writer: '',
        subject: '',
        message: '',
      },
    }
  },

  methods: {
    onchangeFile(e) {
      this.file = e.target.files[0]
      this.fileExtension = e.target.files[0].name.split('.').pop().toLowerCase()
    },

    submitForm() {
      try {
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

          axios.post(`${api}/Post`, data).then(() => {
            this.$router.push('/')
          })
        }
      } catch {
        alert('Please select image')
      }
    },
  },
}
</script>
