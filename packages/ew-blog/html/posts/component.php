<system-ui-view name="comments-card" class="card card-glass card-medium">
  <div class="card-header">
    <h1 v-bind:class="{'inline-loader': loading}"> {{ card_title }} </h1>

    <div class="card-title-action-right">
      <button class="btn btn-circle" v-on:click="reload()"><i class="icon-cw-1"></i></button>
    </div>
  </div>

  <div class="card-content list">
    <div class="card-control-bar">
      <!--      <label class="checkbox">
              Compact view
              <input type="checkbox" v-model="compact_view" /><i></i>
            </label>-->
      <ew-pagination v-bind:list.sync="posts" v-bind:loading.sync="loading" v-bind:filter="filter"></ew-pagination>
    </div>

    <system-spirit animations="verticalShift" vertical-shift="list-item">
      <ul class="list rows" v-bind:class="{'compact' : compact_view}">
        <li class="list-item action" v-for="post in posts.data" v-on:click="showPost(post.content.id)">
          <h3>
            {{ post.id + '. ' +post.content.title }}
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