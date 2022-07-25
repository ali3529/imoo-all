<template>
  <div class="file-upload">
    <div class="form-group">
      <div class="form-group">
        <div v-if="$parent.canEditProperty">
          <!-- Drop Zone -->
          <div id="drop-zone" class="upload-drop-zone p-12 bg-gray-100" @dragover="dragover" @dragleave="dragleave"
               @drop="drop" @click="$refs.file.click()"
          >
            <input id="attachments" ref="file" type="file" multiple="multiple" accept=".jpeg,.png,.mp4,.pdf,.jpg" hidden
                   name="fields[assetsFieldHandle][]" @change="uploadFieldChange"
            >
            Drop files here or click to upload.
          </div>
          <!--        <br>-->
          <small>Upload Center: Pictures(.jpeg, .jpg, .png) / Videos:(only .mp4 ) / Doc:(only .pdf )</small>
        </div>
        <hr>
        <div class="form-group files">
          <div v-for="(attachment) in $parent.attachments" v-cloak class="attachment-holder animated fadeIn">
            <div v-if="attachment.name" class="form-group my-1">
              <span class="label label-primary">
                {{ attachment.name + ' (' + Number((attachment.size / 1024 / 1024).toFixed(1)) + 'MB)' }}
              </span>
              <span v-if="!$parent.canEditProperty()" style="background: red; cursor: pointer;" @click="removeAttachment(attachment)">
                <span
                  class="btn btn-xs btn-danger"
                >{{ $t('remove') }}</span>
              </span>
            </div>
            <div v-else class="form-group my-1">
              <span class="label label-primary">
                <img v-if="isImage(getExt(attachment))" width="100" height="100" :src="attachment">
                <a v-if="isOtherFile(getExt(attachment))" :href="attachment"><img width="100" height="100" class="border bg-gradient"></a>
                <video v-if="isVideo(getExt(attachment))" width="320" height="240" controls>
                  <source :src="attachment" type="video/mp4">
                  Your browser does not support the video tag.
                </video>
              </span>
              <span v-if="!$parent.canEditProperty()" style="background: red; cursor: pointer;" @click="removeAttachment(attachment)">
                <span
                  class="btn btn-xs btn-danger"
                >{{ $t('remove') }}</span>
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'FileManagement',
  methods: {
    uploadFieldChange (e) {
      const files = e.target.files || e.dataTransfer.files
      if (!files.length) {
        return
      }
      for (let i = files.length - 1; i >= 0; i--) {
        this.$parent.attachments.push(files[i])
      }
      // Reset the form to avoid copying these files multiple times into this.attachments
      document.getElementById('attachments').value = []
    },

    removeAttachment (attachment) {
      if (this.$parent.canEditProperty()) return
      this.$parent.attachments.splice(this.$parent.attachments.indexOf(attachment), 1)

      this.getAttachmentSize()
    },

    getAttachmentSize () {
      this.upload_size = 0 // Reset to beginningƒ
      this.$parent.attachments.map((item) => {
        this.upload_size += parseInt(item.size)
      })

      this.upload_size = Number((this.upload_size).toFixed(1))
      // this.$forceUpdate()
    },

    dragover (event) {
      event.preventDefault()
    },

    dragleave (event) {
      // Clean up
    },

    drop (event) {
      event.preventDefault()
      this.$refs.file.files = event.dataTransfer.files
      this.uploadFieldChange(event) // Trigger the onChange event manually
    },

    isImage (type) {
      switch (type.toLowerCase()) {
        case 'image/jpeg':
        case 'image/jpg':
        case 'image/png':
        case 'jpeg':
        case 'jpg':
        case 'png':
          return true
        default:
          return false
      }
    },

    isVideo (type) {
      switch (type.toLowerCase()) {
        case 'video/mp4':
        case 'mp4':
          return true
        default:
          return false
      }
    },

    isOtherFile (type) {
      switch (type.toLowerCase()) {
        case 'application/pdf':
        case 'pdf':
          return true
        default:
          return false
      }
    },

    getExt (url) {
      const words = url.split('.')
      return words[words.length - 1]
    }

  }
}
</script>

<style scoped>
/* layout.css Style */
.upload-drop-zone {
  height: 200px;
  border-width: 2px;
  margin-bottom: 20px;
}

/* skin.css Style*/
.upload-drop-zone {
  color: #ccc;
  border-style: dashed;
  border-color: #ccc;
  line-height: 200px;
  text-align: center
}

.upload-drop-zone.drop {
  color: #222;
  border-color: #222;
}
</style>
