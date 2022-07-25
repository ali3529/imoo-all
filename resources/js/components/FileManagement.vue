<template>
  <div no-body class="mb-1 bg-transparent border-0 mt-4 mb-4">
            <div class="body-bg shadow-none mb-4">
              <div
                id="ac6"
                class="d-flex flex-row justify-content-center border border-1 rounded border-dark"
              >
                <h4 block v-b-toggle.accordion-6 variant="info">Photos and Videos</h4>
              </div>
            </div>
            <b-collapse
              id="accordion-6"
              accordion="my-accordion"
              role="tabpanel"
            >
  <div class="file-upload">
    <div class="form-group">
      <div class="form-group">
        <div>
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
          <div v-for="(attachment,index) in $parent.attachments" :key="index" v-cloak class="attachment-holder animated fadeIn">
            <div v-if="attachment.name" class="form-group my-1 bprder-1 border-dark">
               <img  :src="$parent.url[index]" class="rounded" width="50px" height="50px" />
              <span class="label label-primary">
                {{ attachment.name + ' (' + Number((attachment.size / 1024 / 1024).toFixed(1)) + 'MB)' }}
              </span>
              <!-- <img :src="URL.createObjectURL(attachment[0])"/> -->]
             
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

   <div class="d-flex flex-row justify-content-end gap-3 m-2 mt-3">
                <b-button
                  class="rounded-lg px-4 col-sm-2 text-white text-lg border border-dark"
                  block
                  type="button"
                  v-b-toggle.accordion-3
                  variant="dark"
                  >previous</b-button
                >
                <b-button
                  class="rounded-lg px-6 col-sm-2 text-lg border border-dark"
                  block
                  type="button"
                  v-b-toggle.accordion-4
                  variant="light"
                  >Next Step</b-button
                >
              </div>
    </b-collapse>
    </div>
</template>

<script>
// console.log("ewfjeljf",$parent.attachment);
export default {
  name: 'FileManagement',
 
  methods: {
    
    uploadFieldChange (e) {
      const files = e.target.files || e.dataTransfer.files
         console.log("dsvlsdv_v",e.target.files[0]);
        
      if (!files.length) {
        return
      }
      for (let i = files.length - 1; i >= 0; i--) {
        this.$parent.attachments.push(files[i])
     this.$parent.url.push(URL.createObjectURL(files[i]))
      } 
  
      // Reset the form to avoid copying these files multiple times into this.attachments
      document.getElementById('attachments').value = []

      console.log("dsvlsdv",this.$parent.url);
    },

    removeAttachment (attachment) {
      if (this.$parent.canEditProperty()) return
      this.$parent.attachments.splice(this.$parent.attachments.indexOf(attachment), 1)
      console.log("sdnvdsnv",attachment);
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
