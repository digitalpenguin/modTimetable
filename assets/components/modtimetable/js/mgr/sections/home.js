Ext.onReady(function() {
    MODx.load({ xtype: 'modtimetable-page-home'});
});

modTimetable.page.Home = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        components: [{
            xtype: 'modtimetable-panel-home'
            ,renderTo: 'modtimetable-panel-home-div'
        }]
    });
    modTimetable.page.Home.superclass.constructor.call(this,config);
};
Ext.extend(modTimetable.page.Home,MODx.Component);
Ext.reg('modtimetable-page-home',modTimetable.page.Home);