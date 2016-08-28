<system-ui-view name="comments-card" class="card card-medium z-index-1">
  <div class="card-header">
    <h1> {{ card_title }} </h1>
  </div>
  <div class="card-content list">
    <ul class="list items">
      <li class="list-item" v-for="comment in comments">
        <h3>
          {{ comment.email }}
        </h3>
        <p>
          <strong>{{ comment.name }}</strong> - 
          {{ comment.content }}
        </p>
        <p class="actions">
          <button class="btn btn-text btn-circle icon-delete btn-danger" type="button"></button>
        </p>
      </li>
    </ul>
  </div>
</system-ui-view>
<?= ew\ResourceUtility::load_js_as_tag('ew-blog/html/comments/component.js') ?>