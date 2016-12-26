<system-ui-view name="blocks-card" class="card card-glass card-medium">
  <div class="card-header">
    <h1 v-bind:class="{'inline-loader': loading}"> {{ card_title }} </h1>

    <div class="card-title-action-right">
      <button class="btn btn-circle" v-on:click="reload()"><i class="icon-cw-1"></i></button>
    </div>
  </div>

  <div class="card-content list">
    <div class="card-control-bar">
      <ew-pagination v-bind:list.sync="blocks" 
                     v-bind:loading.sync="loading" 
                     v-bind:filter="filter"></ew-pagination>
    </div>

    <system-spirit animations="verticalShift" vertical-shift="list-item">
      <ul class="list rows">
        <li class="list-item action" v-for="block in blocks.data" v-on:click="show(block.id)">
          <h3>
            {{ block.id + '. ' + block.title }}
            <span>
              {{ block.date_created }}
            </span>
          </h3>
        </li>
      </ul>
    </system-spirit>

  </div>
</system-ui-view>
<?= ew\ResourceUtility::load_js_as_tag(__DIR__ . '/component.js', [], true) ?>

