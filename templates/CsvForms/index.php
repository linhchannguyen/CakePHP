<script type="text/JavaScript">
  $(function() {
      $("div.code_table").css("display", "none");
      $('#school_code table tr:odd').addClass("odd");
      $('#school_code table tr:not(:first-child):even').addClass("even");
      $('#kouza_code table tr:odd').addClass("odd");
      $('#kouza_code table tr:not(:first-child):even').addClass("even");
      $('#event_kind_code table tr:odd').addClass("odd");
      $('#event_kind_code table tr:not(:first-child):even').addClass("even");

      $("#btn_school_code").click(function () {
          $("#school_code").slideToggle('fast');
      });
      $("#btn_kouza_code").click(function () {
          $("#kouza_code").slideToggle('fast');
      });
      $("#btn_event_kind_code").click(function () {
          $("#event_kind_code").slideToggle('fast');
      });
  });
</script>


<?= $this->Form->create(null, ['url' => '/csv_form/csv_news_receive', 'type' => 'file']) ?>
<?= $this->Form->hidden('', ['name' => 'reload_ticket', 'value' => md5(uniqid(mt_rand(), true))]) ?>
<div class="frm">
  <dl>
    <dt><?= __d('csv_form', 'CSV_NEWS_IMPORT_LABEL') ?></dt>
    <dd>
      <div style="display: flex;">
        <?= $this->Form->control('', ['type' => 'file', 'id' => 'datacsv_newsfile0', 'class' => 'form01', 'name' => 'csv_news', 'size' => 30]) ?>&nbsp;&nbsp;&nbsp;
        <?= $this->Form->submit(__d('csv_form', 'CSV_IMPORT_BTN')); ?>
      </div>
    </dd>
  </dl>
</div>
<?= $this->Form->end() ?>

<?= $this->Form->create(null, ['url' => '/csv_form/csv_recommends_receive', 'type' => 'file']) ?>
<?= $this->Form->hidden('', ['name' => 'reload_ticket', 'value' => md5(uniqid(mt_rand(), true))]) ?>
<div class="frm">
  <dl>
  <dt><?= __d('csv_form', 'CSV_RECOMMEND_IMPORT_LABEL') ?></dt>
    <dd>
      <div style="display: flex;">
        <?= $this->Form->control('', ['type' => 'file', 'id' => 'datacsv_recommendsfile0', 'class' => 'form01', 'name' => 'csv_recommends', 'size' => 30]) ?>&nbsp;&nbsp;&nbsp;
        <?= $this->Form->submit(__d('csv_form', 'CSV_IMPORT_BTN')); ?>
      </div>
    </dd>
  </dl>
</div>
<?= $this->Form->end() ?>

<?= $this->Form->create(null, ['url' => '/csv_form/csv_events_receive', 'type' => 'file']) ?>
<?= $this->Form->hidden('', ['name' => 'reload_ticket', 'value' => md5(uniqid(mt_rand(), true))]) ?>
<div class="frm">
  <dl>
    <dt><?= __d('csv_form', 'CSV_EVENT_IMPORT_LABEL') ?></dt>
    <dd>
      <div style="display: flex;">
        <?= $this->Form->control('', ['type' => 'file', 'id' => 'datacsv_eventsfile0', 'class' => 'form01', 'name' => 'csv_events', 'size' => 30]) ?>&nbsp;&nbsp;&nbsp;
        <?= $this->Form->submit(__d('csv_form', 'CSV_IMPORT_BTN')); ?>
      </div>
    </dd>
  </dl>
</div>
<?= $this->Form->end() ?>

<?= $this->Form->create(null, ['url' => '/csv_form/csv_holidays_receive', 'type' => 'file']) ?>
<?= $this->Form->hidden('', ['name' => 'reload_ticket', 'value' => md5(uniqid(mt_rand(), true))]) ?>
<div class="frm">
  <dl>
    <dt><?= __d('csv_form', 'CSV_HOLIDAY_IMPORT_LABEL') ?></dt>
    <dd>
      <div style="display: flex;">
        <?= $this->Form->control('', ['type' => 'file', 'id' => 'datacsv_holidaysfile0', 'class' => 'form01', 'name' => 'csv_holidays', 'size' => 30]) ?>&nbsp;&nbsp;&nbsp;
        <?= $this->Form->submit(__d('csv_form', 'CSV_IMPORT_BTN')); ?>
      </div>
    </dd>
  </dl>
</div>
<?= $this->Form->end() ?>

<p class="warn"><?= __d('csv_form', 'CSV_IMPORT_NOTICE') ?></p>

<div style="display:block"><button id="btn_school_code"><?= __d('csv_form', 'TOGGLE_TABLE_SCHOOL_BTN') ?></button></div>
<div id="school_code" class="code_table">
  <table class="catalog">
    <thead>
        <tr>
            <th>ID</th>
            <th><?= __d('csv_form', 'SCHOOL_NAME_COL') ?></th>
        </tr>
    </thead>
    <tbody>
      <?php foreach($school_code_table as $id => $label): ?>
        <tr>
            <td><?= $id ?></td>
            <td><?= $label ?></td>
        </tr>
      <?php endforeach ?>
    </tbody>
  </table>
</div>

<div style="display:block"><button id="btn_kouza_code"><?= __d('csv_form', 'TOGGLE_TABLE_KOUZA_BTN') ?></button></div>
<div id="kouza_code" class="code_table">
  <table class="catalog">
    <thead>
        <tr>
            <th>ID</th>
            <th><?= __d('csv_form', 'COURSE_NAME_COL') ?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($kouza_code_table as $id => $label): ?>
          <tr>
              <td><?= $id ?></td>
              <td><?= $label ?></td>
          </tr>
        <?php endforeach ?>
    </tbody>
  </table>
</div>

<div style="display:block"><button id="btn_event_kind_code"><?= __d('csv_form', 'TOGGLE_TABLE_EVENT_TYPE_BTN') ?></button></div>
<div id="event_kind_code" class="code_table">
  <table class="catalog">
    <thead>
        <tr>
            <th>ID</th>
            <th><?= __d('csv_form', 'EVENT_NAME_COL') ?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($event_kind_code_table as $id => $label): ?>
          <tr>
              <td><?= $id ?></td>
              <td><?= $label ?></td>
          </tr>
        <?php endforeach ?>
    </tbody>
  </table>
</div>