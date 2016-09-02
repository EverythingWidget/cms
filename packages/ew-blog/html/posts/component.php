<system-ui-view name="comments-card" class="card card-medium z-index-1">
  <div class="card-header">
    <h1> {{ card_title }} </h1>
  </div>
  <div class="card-content list">
    <div class="card-control-bar">
<!--      <label class="checkbox">
        Compact view
        <input type="checkbox" v-model="compact_view" /><i></i>
      </label>-->

      <ew-pagination v-bind:list.sync="posts"></ew-pagination>      
    </div>

    <system-spirit animations="liveHeight,verticalShift" vertical-shift="list-item">
      <ul class="list items" v-bind:class="{'compact' : compact_view}">
        <li class="list-item action" v-for="post in posts.data" track-by='id' v-on:click="showPost(post)">
          <h3>
            {{ post.content.title }}
            <span>
              {{ post.date_published }}
            </span>
          </h3>        
          <p class="secondary">
            <strong v-if="post.draft">Draft</strong> 
          </p>
          <p class="secondary">
            <strong>Comments - </strong> 
            {{ getCommentStatus(post).title }}
          </p>
        </li>
      </ul>
    </system-spirit>
  </div>
</system-ui-view>
<?= ew\ResourceUtility::load_js_as_tag('ew-blog/html/posts/component.js') ?>