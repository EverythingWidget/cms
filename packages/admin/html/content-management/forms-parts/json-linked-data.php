<div class="form-block">  
  <system-field class="field">
    <label>tr{Show comments}</label>
    <select class="text-field" v-model="selected">
      <option v-for="schema in schemas" v-bind:value="schema.structure" >{{ schema.title }}</option>
    </select>
  </system-field> 
</div>

<div class="form-block">
  <system-input-json name="feeder" id="feeder" data-content-type="list" v-bind:value="selected"></system-input-json>
</div>

<script>
  (function () {
    var schemas = [
      {
        title: 'None',
        structure: {}
      },
      {
        title: 'Thing',
        structure: {
          "@context": "http://schema.org/",
          "@type": "Thing"
        }
      },
      {
        title: 'Person',
        structure: {
          "@context": "http://schema.org/",
          "@type": "Person",
          "name": "",
          "jobTitle": "",
          "telephone": "",
          "url": ""
        }
      },
      {
        title: 'Event',
        structure: {
          "@context": {
            "ical": "http://www.w3.org/2002/12/cal/ical#",
            "xsd": "http://www.w3.org/2001/XMLSchema#",
            "ical:dtstart": {
              "@type": "xsd:dateTime"
            }
          },
          "ical:summary": "",
          "ical:location": "",
          "ical:dtstart": ""
        }
      },
      {
        title: 'Activity',
        structure: {
          "@context": "http://www.w3.org/ns/activitystreams",
          "@type": "Create",
          "actor": {
            "@type": "Person",
            "@id": "acct:sally@example.org",
            "displayName": "Sally"
          },
          "object": {
            "@type": "Note",
            "content": "This is a simple note"
          },
          "published": "2015-01-25T12:34:56Z"
        }
      }
    ];

    var vue = new Vue({
      el: '#json-linked-data',
      data: {
        selected: schemas[0].structure,
        schemas: schemas
      }
    });
  })();
</script>