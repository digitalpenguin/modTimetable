var modTimetable = function(config) {
    config = config || {};
modTimetable.superclass.constructor.call(this,config);
};
Ext.extend(modTimetable,Ext.Component,{
    page:{},window:{},grid:{},tree:{},panel:{},combo:{},config: {}
});
Ext.reg('modtimetable',modTimetable);
modTimetable = new modTimetable();