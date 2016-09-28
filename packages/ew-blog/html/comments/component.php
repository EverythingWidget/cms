<system-ui-view name="comments-card" class="card card-medium z-index-1">
  <div class="card-header">
    <h1> {{ card_title }} </h1>
  </div>
  <div class="card-content list">
    <ew-pagination v-bind:list.sync="comments" v-bind:filter="filter"></ew-pagination>

    <system-spirit animations="liveHeight,verticalShift" vertical-shift="list-item">
      <ul class="list items">
        <li class="list-item" v-for="comment in comments.data">
          <h3>
            {{ comment.email }}
          </h3>
          <p>
            <strong>{{ comment.name }}</strong> - 
            {{ comment.content }}
          </p>
          <p class="actions">
            <button class="btn btn-text btn-circle btn-success" type="button" v-on:click="confirmComment(comment.id)">
              <i class="icon-check" ></i>
            </button>
            <button class="btn btn-text btn-circle btn-danger" type="button" v-on:click="deleteComment(comment.id)">
              <i class="icon-trash-empty"></i>
            </button>          
          </p>
        </li>
      </ul>
    </system-spirit>
  </div>
</system-ui-view>
<?= ew\ResourceUtility::load_js_as_tag('ew-blog/html/comments/component.js') ?>