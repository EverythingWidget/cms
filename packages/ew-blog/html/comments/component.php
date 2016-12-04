<system-ui-view name="comments-card" class="card card-medium z-index-1">
  <div class="card-header">
    <h1 v-bind:class="{'inline-loader': loading}"> {{ card_title }} </h1>

    <div class="card-title-action-right">
      <button class="btn btn-circle" v-on:click="reloadComments()"><i class="icon-cw-1"></i></button>
    </div>

  </div>
  <div class="card-content list">
    <div class="card-control-bar" >
      <div class="radio-groups">
        <label class="radio">          
          <input type="radio" v-model="show" value="new"/><i></i>
          <span>New</span>
        </label>

        <label class="radio">          
          <input type="radio" v-model="show" value="confirmed"/><i></i>
          <span>Confirmed</span>
        </label>
      </div>

      <ew-pagination v-bind:list.sync="comments" v-bind:filter="filter" v-bind:loading.sync="loading"></ew-pagination>
    </div>

    <system-spirit animations="liveHeight,verticalShift" vertical-shift="list-item">
      <ul class="list items">
        <li class="list-item" v-for="comment in comments.data">
          <h3>
            {{ comment.email }}
          </h3>
          <h3>
            {{ comment.name }}
          </h3>
          <p class="pre-text" dir="auto">{{ comment.content }}</p>
          <a class="text-link" v-on:click="showPost(comment.ew_content.id)">
            {{ comment.ew_content.title }}
          </a>
          <p class="actions">
            <button class="btn btn-text btn-circle btn-success" type="button" v-if="show === 'new'" v-on:click="confirmComment(comment.id)">
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