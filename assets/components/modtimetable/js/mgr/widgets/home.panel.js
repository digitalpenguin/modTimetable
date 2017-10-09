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
            xtype: 'modx-panel'
            ,items: [{
                xtype: 'modtimetable-breadcrumbs-panel',
                style:'box-shadow: 1px 1px 2px #bbb;',
                id: 'timetable-breadcrumbs',
                desc: '<p>'+_('modtimetable.timetable.intro_msg')+'</p>',
                root : {
                    text : 'Timetable List'
                    ,className: 'first'
                    ,root: true
                    ,pnl: 'modtimetable-grid-timetables'
                }
            },{
                layout:'card'
                ,id:'timetable-card-container'
                ,style:'box-shadow: 1px 1px 2px #ccc;'
                ,activeItem:0
                ,border: false
                ,autoHeight: true
                ,defaults:{
                    cls: 'main-wrapper'
                    ,autoHeight: true
                }
                ,items: [{
                    xtype: 'modtimetable-grid-timetables',
                    id: 'modtimetable-grid-timetables'
                },{
                    xtype: 'modx-panel',
                    id: 'modtimetable-panel-days'
                },{
                    xtype: 'modx-panel',
                    id: 'modtimetable-panel-sessions'
                }]
            }]
        }]
    });
    modTimetable.panel.Home.superclass.constructor.call(this,config);
};
Ext.extend(modTimetable.panel.Home,MODx.Panel);
Ext.reg('modtimetable-panel-home',modTimetable.panel.Home);
