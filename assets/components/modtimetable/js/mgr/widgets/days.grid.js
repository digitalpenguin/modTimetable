modTimetable.grid.Days = function(config) {
    config = config || {};
    this.actionColTpl = new Ext.XTemplate('<tpl for=".">'
        +'{control_buttons}'
            +'<tpl if="actions !== null">'
                +'<ul class="actions">'
                    +'<tpl for="actions">'
                        +'<li><button type="button" class="controlBtn {className}">{text}</button></li>'
                    +'</tpl>'
                +'</ul>'
            +'</tpl>'
        +'</tpl>', {
        compiled: true
    });
    Ext.applyIf(config,{
        id: 'modtimetable-grid-days'
        ,url: modTimetable.config.connectorUrl
        ,baseParams: {
            action: 'mgr/day/getlist'
        }
        ,save_action: 'mgr/day/updatefromgrid'
        ,autosave: true
        ,fields: ['id','name','description','num_in_week','image','position']
        ,autoHeight: true
        ,paging: true
        ,remoteSort: true
        ,ddGroup: 'modtimetableDayDDGroup'
        ,enableDragDrop: true
        ,columns: [{
            header: _('id')
            ,dataIndex: 'id'
            ,width: 40
        },{
            header: _('modtimetable.day.name')
            ,dataIndex: 'name'
            ,width: 100
            ,editor: { xtype: 'textfield' }
        },{
            header: _('modtimetable.day.action')
            ,dataIndex:'control_buttons'
            ,width:250
            ,fixed:true
            ,renderer: { fn: this.actionColumnRenderer, scope: this }
        },{
            header: _('modtimetable.day.description')
            ,dataIndex: 'description'
            ,width: 250
            ,editor: { xtype: 'textfield' }
        },{
            header: _('modtimetable.day.position')
            ,dataIndex: 'position'
            ,width: 50
            ,editor: { xtype: 'numberfield', allowDecimal: false, allowNegative: false }
        }]
        ,tbar: [{
            text: '<i style="margin-right:3px;" class="icon icon-arrow-left"></i> '+_('modtimetable.day.backtotimetables')
            ,handler: this.backToTimetableGrid
            ,scope: this
        },'->',{
            text: '<i style="margin-right:3px;" class="icon icon-plus"></i> '+_('modtimetable.day.create')
            ,handler: this.createDay
            ,scope: this
        },{
            xtype: 'textfield'
            ,emptyText: _('modtimetable.global.search') + '...'
            ,listeners: {
                'change': {fn:this.search,scope:this}
                ,'render': {fn: function(cmp) {
                    new Ext.KeyMap(cmp.getEl(), {
                        key: Ext.EventObject.ENTER
                        ,fn: function() {
                            this.fireEvent('change',this);
                            this.blur();
                            return true;
                        }
                        ,scope: cmp
                    });
                },scope:this}
            }
        }]
        ,listeners: {
            'render': function(g) {
                var ddrow = new Ext.ux.dd.GridReorderDropTarget(g, {
                    copy: false
                    ,listeners: {
                        'beforerowmove': function(objThis, oldIndex, newIndex, records) {
                        }

                        ,'afterrowmove': function(objThis, oldIndex, newIndex, records) {

                            MODx.Ajax.request({
                                url: modTimetable.config.connectorUrl
                                ,params: {
                                    action: 'mgr/day/reorder'
                                    ,idItem: records.pop().id
                                    ,oldIndex: oldIndex
                                    ,newIndex: newIndex
                                }
                                ,listeners: {

                                }
                            });
                        }

                        ,'beforerowcopy': function(objThis, oldIndex, newIndex, records) {
                        }

                        ,'afterrowcopy': function(objThis, oldIndex, newIndex, records) {
                        }
                    }
                });

                Ext.dd.ScrollManager.register(g.getView().getEditorParent());
            }
            ,beforedestroy: function(g) {
                Ext.dd.ScrollManager.unregister(g.getView().getEditorParent());
            }

        }
    });
    modTimetable.grid.Days.superclass.constructor.call(this,config);
    this.on('click', this.onClick, this);
};
Ext.extend(modTimetable.grid.Days,MODx.grid.Grid,{
    windows: {}

    ,getMenu: function() {
        var m = [];
        m.push({
            text: _('modtimetable.day.update')
            ,handler: this.updateDay
        });
        m.push('-');
        m.push({
            text: _('modtimetable.day.remove')
            ,handler: this.removeDay
        });
        this.addContextMenuItem(m);
    }

    ,backToTimetableGrid: function() {
        Ext.getCmp('modtimetable-grid-timetables').activate();
    }


    ,actionColumnRenderer: function (value, metaData, record, rowIndex, colIndex, store){
        var rec = record.data;
        var values = { sessions: '' };
        var h = [];
        h.push({ className:'editDay', text: '<i class="icon icon-edit"></i> '+_('modtimetable.day.edit') });
        h.push({ className:'viewSessions', text: '<i class="icon icon-clock-o"></i> '+_('modtimetable.day.view_sessions') });

        values.actions = h;
        return this.actionColTpl.apply(values);
    }

    ,onClick: function(e){
        var t = e.getTarget();
        var elm = t.className.split(' ')[0];
        if(elm === 'controlBtn'){
            var act = t.className.split(' ')[1];
            var record = this.getSelectionModel().getSelected();
            this.menu.record = record.data;
            switch (act) {
                case 'editDay':
                    this.updateDay(record, e);
                    break;
                case 'viewDays':
                    alert('Viewing Days');
                    //this.viewLessons(record, e);
                    break;
                default:
                    break;
            }
        }
    }

    ,createDay: function(btn,e) {

        var createDay = MODx.load({
            xtype: 'modtimetable-window-day'
            ,listeners: {
                'success': {fn:function() { this.refresh(); },scope:this}
            }
        });
        createDay.fp.getForm().findField('timetableId').setValue(this.timetableId);
        createDay.show(e.target);
    }

    ,updateDay: function(btn,e,isUpdate) {
        if (!this.menu.record || !this.menu.record.id) return false;

        var updateDay = MODx.load({
            xtype: 'modtimetable-window-day'
            ,title: _('modtimetable.day.update')
            ,action: 'mgr/day/update'
            ,record: this.menu.record
            ,listeners: {
                'success': {fn:function() { this.refresh(); },scope:this}
            }
        });

        updateDay.fp.getForm().reset();
        updateDay.fp.getForm().setValues(this.menu.record);
        updateDay.show(e.target);
    }
    
    ,removeDay: function(btn,e) {
        if (!this.menu.record) return false;
        
        MODx.msg.confirm({
            title: _('modtimetable.day.remove')
            ,text: _('modtimetable.day.remove_confirm')
            ,url: this.config.url
            ,params: {
                action: 'mgr/day/remove'
                ,id: this.menu.record.id
            }
            ,listeners: {
                'success': {fn:function(r) { this.refresh(); },scope:this}
            }
        });
    }

    ,search: function(tf,nv,ov) {
        var s = this.getStore();
        s.baseParams.query = tf.getValue();
        this.getBottomToolbar().changePage(1);
        this.refresh();
    }
    
    ,getDragDropText: function(){
        return this.selModel.selections.items[0].data.name;
    }
});
Ext.reg('modtimetable-grid-days',modTimetable.grid.Days);

modTimetable.window.Day = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        title: _('modtimetable.day.create')
        ,closeAction: 'close'
        ,url: modTimetable.config.connectorUrl
        ,action: 'mgr/day/create'
        ,fields: [{
            xtype: 'textfield'
            ,name: 'id'
            ,hidden: true
        },{
            xtype: 'textfield'
            ,name: 'timetableId'
            ,hidden: true
        },{
            xtype: 'textfield'
            ,fieldLabel: _('name')
            ,name: 'name'
            ,anchor: '100%'
        },{
            xtype: 'textarea'
            ,fieldLabel: _('description')
            ,name: 'description'
            ,anchor: '100%'
        },{
            xtype: 'textfield'
            ,name: 'position'
            ,hidden: true
        }]
    });
    modTimetable.window.Day.superclass.constructor.call(this,config);
};
Ext.extend(modTimetable.window.Day,MODx.Window);
Ext.reg('modtimetable-window-day',modTimetable.window.Day);

