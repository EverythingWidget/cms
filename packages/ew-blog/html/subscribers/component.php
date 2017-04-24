<system-ui-view name="subscribers-card" class="card card-medium z-index-1">
  <div class="card-header">
    <h1> {{ card_title }} </h1>

    <div class="card-title-action-right">
      <button class="btn btn-circle" v-on:click="reload()"><i class="icon-cw-1"></i></button>
    </div>
  </div>

  <div class="card-content list">
    <div class="card-control-bar">
      <ew-pagination v-bind:list.sync="subscribers"></ew-pagination>
    </div>

    <system-spirit animations="liveHeight,verticalShift" vertical-shift="list-item">
      <ul class="list items" >
        <li class="list-item" v-for="subscribers in subscribers.data">
          <h3>
            {{ subscribers.id }}
            <span>
              {{ subscribers.date_created }}
            </span>
          </h3>   
          <p>
            {{ subscribers.email }}
          </p>
          <p>
            {{ subscribers.options }}
          </p>
          <p class="actions">
            <button class="btn btn-text btn-circle btn-danger" type="button" v-on:click="deleteSubscriber(subscribers.id)">
              <i class="icon-trash-empty"></i>
            </button>
          </p>
        </li>
      </ul>
    </system-spirit>

  </div>
</system-ui-view>

<?= ew\ResourceUtility::load_js_as_tag('ew-blog/html/subscribers/component.js') ?>
