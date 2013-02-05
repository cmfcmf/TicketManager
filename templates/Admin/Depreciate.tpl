{include file='Admin/Header.tpl' icon='' __title='Depreciate'}

{form cssClass='z-form'}
    {formerrormessage id='error'}
    {formvalidationsummary}
    <fieldset>
        <legend>{gt text='Depreciate tickets'}</legend>
        <div class="z-formrow">
            {formlabel __text='QR-Code' for='qrCode' mandatorysym=true}
            {formtextinput id='qrCode' maxLength='30' text='' mandatory=true readOnly=false}
        </div>
        <div class="z-formrow">
            {formlabel __text='Mode' for='mode' mandatorysym=true}
            {formdropdownlist id='mode' size=1 items=$modes  selectedValue=-1 mandatory=true}
        </div>
        <div class="z-formrow">
            {formlabel text='Dry run' for='dryRun' mandatorysym=false}
            {formcheckbox id='dryRun' checked=false mandatory=false readOnly=false}
        </div>
        <div class="z-buttons z-formbuttons">
            {formbutton commandName='depreciate' __text='Depreciate' class='z-bt-ok'}
            {formbutton commandName='cancel' __text='Cancel' class='z-bt-cancel'}
        </div>
    </fieldset>

{/form}

{include file='Admin/Footer.tpl'}
