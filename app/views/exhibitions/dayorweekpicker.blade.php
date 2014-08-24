<table role="grid" aria-labelledby="@{{uniqueId}}-title" aria-activedescendant="@{{activeDateId}}" dayorweekpicker>
  <thead>
    <tr>
        <th colspan="@{{7 + showWeeks}}">
            <button id="@{{uniqueId}}-by-day" 
                role="heading" 
                aria-live="assertive" 
                aria-atomic="true" 
                type="button" 
                class="btn btn-default btn-sm pull-left" 
                ng-class="{active: !selectedWeek}"
                ng-click="selectBy('day')"
                tabindex="-1" 
                style="width:40%;">
                <strong>Por día</strong>
            </button>
            <button id="@{{uniqueId}}-by-week" 
                role="heading" 
                aria-live="assertive" 
                aria-atomic="true"
                ng-class="{active: selectedWeek}"
                type="button" 
                class="btn btn-default btn-sm pull-right" 
                ng-click="selectBy('week')"
                tabindex="-1" 
                style="width:60%;">
                <strong>Por semana</strong>
            </button>
        </th>
    </tr>
    <tr>
      <th colspan="@{{7 + showWeeks}}">
        <lable class="btn btn-default btn-sm" 
        	style="width:100%" 
        	flm-filters 
        	filter-name="month" 
        	filter-value="0">
        	@{{title}}
        </lable>
      </th>
    </tr>
    <tr>
      <th ng-show="showWeeks" class="text-center"></th>
      <th ng-repeat="label in labels track by $index" class="text-center"><small aria-label="@{{label.full}}">@{{label.abbr}}</small></th>
    </tr>
  </thead>
  <tbody>
    <tr ng-repeat="row in rows track by $index" 
        init
        ng-class="{ 'exhibition-datepicker-week-row': selectedWeek}">
      <td ng-show="showWeeks" class="text-center h6"><em>@{{ weekNumbers[$index] }}</em></td>
      <td ng-repeat="dt in row track by dt.date" class="text-center" role="gridcell" id="@{{dt.uid}}" aria-disabled="@{{!!dt.disabled}}">
        <button type="button" 
        	style="width:100%;" 
        	class="btn btn-default btn-sm" 
        	ng-class="{'btn-info': dt.selected, active: isActive(dt) || isActiveWeek(dt.date, selectedWeek)}" 
            ng-click="select(dt.date)"
        	flm-filters 
        	filter-name="@{{filter}}" 
        	filter-value="@{{dt.date}}" 
        	ng-disabled="dt.disabled" 
        	tabindex="-1">
        		<span ng-class="{'text-muted': dt.secondary, 'text-info': dt.current}">@{{dt.label}}</span>
        </button>
      </td>
    </tr>
  </tbody>
</table>