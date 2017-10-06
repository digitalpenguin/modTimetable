modTimetable.panel.Home = function(config) {
    config = config || {};
    Ext.apply(config,{
        border: false
        ,baseCls: 'modx-formpanel'
        ,cls: 'container'
        ,items: [{
            html: '<h2>'+_('modtimetable')+'</h2>'
            ,border: false
            ,cls: 'modx-page-header'
        },{
            xtype: 'modx-tabs'
            ,defaults: { border: false ,autoHeight: true }
            ,border: true
            ,activeTab: 0
            ,hideMode: 'offsets'
            ,items: [{
                title: _('modtimetable.item.items')
                ,items: [{
                    html: '<p>'+_('modtimetable.item.intro_msg')+'</p>'
                    ,border: false
                    ,bodyCssClass: 'panel-desc'
                },{
                    xtype: 'modtimetable-grid-items'
                    ,preventRender: true
                    ,cls: 'main-wrapper'
                }]
            }]
        }]
    });
    modTimetable.panel.Home.superclass.constructor.call(this,config);
};
Ext.extend(modTimetable.panel.Home,MODx.Panel);
Ext.reg('modtimetable-panel-home',modTimetable.panel.Home);
