/**
 * A breadcrumb builder + the panel desc if necessary
 *
 * @class modTimetable.BreadcrumbsPanel
 * @extends MODx.BreadcrumbsPanel
 * @param {Object} config An object of options.
 * @xtype modTimetable-breadcrumbs-panel
 */
modTimetable.BreadcrumbsPanel = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        frame:false
        ,plain:true
        ,border: false
        ,desc: 'This the description part of this panel'
        ,bdMarkup: '<tpl if="typeof(trail) != &quot;undefined&quot;">'
        +'<div class="crumb_wrapper"><ul class="crumbs">'
        +'<tpl for="trail">'
        +'<li{[values.className != undefined ? \' class="\'+values.className+\'"\' : \'\' ]}>'
        +'<tpl if="typeof pnl != \'undefined\'">'
        +'<button type="button" class="controlBtn {pnl}{[values.root ? \' root\' : \'\' ]}">{text}</button>'
        +'</tpl>'
        +'<tpl if="typeof bc != \'undefined\'">'
        +'<button type="button" class="controlBtn bc{[values.root ? \' root\' : \'\' ]}">{text}</button>'
        +'</tpl>'
        +'<tpl if="typeof pnl == \'undefined\' && typeof bc == \'undefined\'"><span class="text{[values.root ? \' root\' : \'\' ]}">{text}</span></tpl>'
        +'</li>'
        +'</tpl>'
        +'</ul></div>'
        +'</tpl>'
        +'<tpl if="typeof(text) != &quot;undefined&quot;">'
        +'<div class="panel-desc{[values.className != undefined ? \' \'+values.className+\'"\' : \'\' ]}"><p>{text}</p></div>'
        +'</tpl>'
        ,root : {
            text : 'Home'
            ,className: 'first'
            ,root: true
            ,pnl: ''
        }
        ,bodyCssClass: 'breadcrumbs'
    });
    modTimetable.BreadcrumbsPanel.superclass.constructor.call(this,config);
    this.on('render', this.init, this);
};

Ext.extend(modTimetable.BreadcrumbsPanel,Ext.Panel,{
    data: {trail: []}

    ,init: function(){
        this.tpl = new Ext.XTemplate(this.bdMarkup, { compiled: true });
        this.reset(this.desc);

        this.body.on('click', this.onClick, this);
    }

    ,getResetText: function(srcInstance){
        if(typeof(srcInstance) != 'object' || srcInstance == null){
            return srcInstance;
        }
        var newInstance = srcInstance.constructor();
        for(var i in srcInstance){
            newInstance[i] = this.getResetText(srcInstance[i]);
        }
        //The trail is not a link
        if(newInstance.hasOwnProperty('pnl')){
            delete newInstance['pnl'];
        }
        return newInstance;
    }

    ,updateDetail: function(data){
        this.data = data;
        // Automagically the trail root
        if(data.hasOwnProperty('trail')){
            var trail = data.trail;
            trail.unshift(this.root);
        }
        this._updatePanel(data);
    }

    ,getData: function() {
        return this.data;
    }

    ,reset: function(msg){
        if(typeof(this.resetText) == "undefined"){
            this.resetText = this.getResetText(this.root);
        }
        this.data = { text : msg ,trail : [this.resetText] };
        this._updatePanel(this.data);
    }

    ,onClick: function(e){
        var target = e.getTarget();

        var index = 1;
        var parent = target.parentElement;
        while ((parent = parent.previousSibling) != null) {
            index += 1;
        }

        var remove = this.data.trail.length - index;
        while (remove > 0) {
            this.data.trail.pop();
            remove -= 1;
        }

        elm = target.className.split(' ')[0];
        if(elm != "" && elm == 'controlBtn'){
            // Don't use "pnl" shorthand, it make the breadcrumb fail
            var panel = target.className.split(' ')[1];

            if (panel == 'bc') {
                alert('here');
                var last = this.data.trail[this.data.trail.length - 1];
                if (last != undefined && last.rec != undefined) {
                    this.data.trail.pop();
                    //var grid = Ext.getCmp('modtimetable-grid-days');

                }
            } else {
                Ext.getCmp(panel).activate();
            }
        }
    }

    ,_updatePanel: function(data){
        this.body.hide();
        this.tpl.overwrite(this.body, data);
        this.body.slideIn('r', {stopFx:true, duration:.2});
        setTimeout(function(){
            Ext.getCmp('modx-content').doLayout();
        }, 500);
    }
});
Ext.reg('modtimetable-breadcrumbs-panel',modTimetable.BreadcrumbsPanel);