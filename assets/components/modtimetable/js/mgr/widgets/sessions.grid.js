modTimetable.grid.Sessions = function(config) {
    config = config || {};
    var me = this;
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
        id: 'modtimetable-grid-sessions'
        ,url: modTimetable.config.connectorUrl
        ,baseParams: {
            action: 'mgr/session/getlist'
        }
        ,save_action: 'mgr/session/updatefromgrid'
        ,autosave: true
        ,fields: ['id','name','teacher','description','start_time','end_time','image','active','position']
        ,autoHeight: true
        ,paging: true
        ,remoteSort: true
        ,ddGroup: 'modtimetableSessionDDGroup'
        ,enableDragDrop: true
        ,columns: [{
            header: _('id')
            ,dataIndex: 'id'
            ,width: 40
        },{
            header: _('modtimetable.session.name')
            ,dataIndex: 'name'
            ,width: 100
            ,editor: { xtype: 'textfield' }
        },{
            header: _('modtimetable.session.action')
            ,dataIndex:'control_buttons'
            ,width:300
            ,fixed:true
            ,renderer: { fn: this.actionColumnRenderer, scope: this }
        },{
            header: _('modtimetable.session.start_time')
            ,dataIndex: 'start_time'
            ,width: 100
            ,editor: { xtype: 'timefield'
                ,format: modTimetable.config.timepicker_format
                ,increment: modTimetable.config.timepicker_minute_interval
                ,minValue: modTimetable.config.timepicker_min_time
                ,maxValue: modTimetable.config.timepicker_max_time
            }
        },{
            header: _('modtimetable.session.end_time')
            ,dataIndex: 'end_time'
            ,width: 100
            ,editor: { xtype: 'timefield'
                ,format: modTimetable.config.timepicker_format
                ,increment: modTimetable.config.timepicker_minute_interval
                ,minValue: modTimetable.config.timepicker_min_time
                ,maxValue: modTimetable.config.timepicker_max_time
            }
        },{
            header: _('modtimetable.session.teacher')
            ,dataIndex: 'teacher'
            ,width: 100
            ,editor: { xtype: 'textfield' }
        },{
            header: _('modtimetable.session.description')
            ,dataIndex: 'description'
            ,width: 250
            ,editor: { xtype: 'textfield' }
        },{
            header: _('modtimetable.session.active')
            ,dataIndex: 'active'
            ,width: 50
            ,renderer: function(value,metadata,record) {
                if(value) {
                    return '<i style="color:#4BB543;" class="icon icon-check"></i>';
                } else {
                    return '<i style="color:#FF0000;" class="icon icon-close"></i>';
                }
            }
        }/*,{
            header: _('modtimetable.session.image')
            ,dataIndex: 'image'
            ,width: 150
            ,fixed:true
            ,sortable:false
            ,renderer: function(value,metadata,record){
                if(!value){return;}
                if(value.charAt(0) !== '/') {
                    value = '/'+value;
                }
                return '<img style="width:100%;" src="' + value + '" />';
            }
        }*/,{
            header: _('modtimetable.session.position')
            ,dataIndex: 'position'
            ,width: 50
            ,hidden:true
            ,editor: { xtype: 'numberfield', allowDecimal: false, allowNegative: false }
        }]
        ,tbar: [{
            text: '<i style="margin-right:3px;" class="icon icon-arrow-left"></i> '+_('modtimetable.session.backtodays')
            ,handler: this.backToDayGrid
            ,scope: this
        },{
            text: '<i style="margin-right:3px;" class="icon icon-plus"></i> '+_('modtimetable.session.create')
            ,handler: this.createSession
            ,scope: this
        },{
            xtype:'tbtext',
            text:'<h2 style="font-size:22px; margin-top:2px; color:#aaa;">'+config.dayRecord.data['name']+' '+_('modtimetable.session.day_sessions')+'</h2>'
        },'->',{
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
                                    action: 'mgr/session/reorder'
                                    ,idItem: records.pop().id
                                    ,oldIndex: oldIndex
                                    ,newIndex: newIndex
                                    ,dayId: config.dayId
                                }
                                ,listeners: {
                                    'success': {fn:function() { me.refresh(); },scope:this}
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
    modTimetable.grid.Sessions.superclass.constructor.call(this,config);
    this.on('click', this.onClick, this);
};
Ext.extend(modTimetable.grid.Sessions,MODx.grid.Grid,{
    windows: {}

    ,getMenu: function() {
        var m = [];
        m.push({
            text: _('modtimetable.session.update')
            ,handler: this.updateSession
        });
        m.push('-');
        m.push({
            text: _('modtimetable.session.remove')
            ,handler: this.removeSession
        });
        this.addContextMenuItem(m);
    }

    ,backToDayGrid: function() {
        Ext.getCmp('modtimetable-grid-days').activate();
    }


    ,actionColumnRenderer: function (value, metaData, record, rowIndex, colIndex, store){
        var rec = record.data;
        var values = { sessions: '' };
        var h = [];
        h.push({ className:'editSession', text: '<i class="icon icon-edit"></i> '+_('modtimetable.session.edit') });

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
                case 'editSession':
                    this.updateSession(record, e);
                    break;
                default:
                    break;
            }
        }
    }

    // Makes this card the active one
    ,activate: function() {
        var cardLayout = Ext.getCmp('timetable-card-container').getLayout();
        var oldRecord = cardLayout.activeItem.record;
        cardLayout.setActiveItem(this.id);
        this.refresh();
        this.resetBreadcrumbs('Sessions');
        cardLayout.activeItem.fireEvent('sessionsactivated',oldRecord);
    }

    // Adds a new breadcrumb to the trail
    ,updateBreadcrumbs: function(msg, rec){
        var bc = Ext.getCmp('timetable-breadcrumbs');
        var bd = bc.getData();
        bd.text = msg;

        bd.trail.shift();
        if (bd.trail.length > 0) {
            bd.trail[bd.trail.length - 1].install = true;
        }
        var newBcItem = {
            text : 'Session: '+rec.data['name']
            ,rec: rec
        };
        bd.trail.push(newBcItem);
        bc.updateDetail(bd);
    }

    // Returns breadcrumbs back to initial state.
    ,resetBreadcrumbs: function(msg, highlight){
        msg = Ext.getCmp('timetable-breadcrumbs').desc;
        if(highlight){
            msg.text = msg;
            msg.className = 'highlight';
        }
        Ext.getCmp('timetable-breadcrumbs').reset(msg);
    }

    ,createSession: function(btn,e) {
        var createSession = MODx.load({
            xtype: 'modtimetable-window-session'
            ,listeners: {
                'success': {fn:function() { this.refresh(); },scope:this}
            }
        });
        createSession.fp.getForm().findField('dayId').setValue(this.dayId);
        createSession.show(e.target);
    }

    ,updateSession: function(btn,e,isUpdate) {
        if (!this.menu.record || !this.menu.record.id) return false;

        var updateSession = MODx.load({
            xtype: 'modtimetable-window-session'
            ,title: _('modtimetable.session.update')
            ,action: 'mgr/session/update'
            ,record: this.menu.record
            ,listeners: {
                'success': {fn:function() { this.refresh(); },scope:this}
            }
        });

        updateSession.fp.getForm().reset();
        updateSession.fp.getForm().setValues(this.menu.record);
        updateSession.fp.getForm().findField('dayId').setValue(this.dayId);
        updateSession.show(e.target);
    }
    
    ,removeSession: function(btn,e) {
        if (!this.menu.record) return false;
        
        MODx.msg.confirm({
            title: _('modtimetable.session.remove')
            ,text: _('modtimetable.session.remove_confirm')
            ,url: this.config.url
            ,params: {
                action: 'mgr/session/remove'
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
Ext.reg('modtimetable-grid-sessions',modTimetable.grid.Sessions);

modTimetable.window.Session = function(config) {
    config = config || {};
    var me = this;
    Ext.applyIf(config,{
        title: _('modtimetable.session.create')
        ,closeAction: 'close'
        ,width:700
        ,url: modTimetable.config.connectorUrl
        ,action: 'mgr/session/create'
        ,fields: [{
            layout: 'column'
            ,items: [{
                layout: 'form'
                ,columnWidth: .5
                ,items: [{
                    xtype: 'textfield'
                    ,name: 'id'
                    ,hidden: true
                },{
                    xtype: 'textfield'
                    ,name: 'dayId'
                    ,hidden: true
                },{
                    xtype: 'textfield'
                    ,fieldLabel: _('name')
                    ,name: 'name'
                    ,anchor: '100%'
                },{
                    xtype: 'textfield'
                    ,fieldLabel: _('modtimetable.session.teacher')
                    ,name: 'teacher'
                    ,anchor: '100%'
                },{
                    layout:'column'
                    ,items:[{
                        layout: 'form'
                        ,columnWidth: .5
                        ,items: [{
                            xtype: 'timefield'
                            ,fieldLabel: _('modtimetable.session.start_time')
                            ,name: 'start_time'
                            ,format: modTimetable.config.timepicker_format
                            ,increment: modTimetable.config.timepicker_minute_interval
                            ,minValue: modTimetable.config.timepicker_min_time
                            ,maxValue: modTimetable.config.timepicker_max_time
                            ,anchor: '100%'
                        }]
                    },{
                        layout: 'form'
                        ,columnWidth: .5
                        ,items: [{
                            xtype: 'timefield'
                            ,fieldLabel: _('modtimetable.session.end_time')
                            ,name: 'end_time'
                            ,format: modTimetable.config.timepicker_format
                            ,increment: modTimetable.config.timepicker_minute_interval
                            ,minValue: modTimetable.config.timepicker_min_time
                            ,maxValue: modTimetable.config.timepicker_max_time
                            ,anchor: '100%'
                        }]
                    }]
                },{
                    xtype: 'textarea'
                    ,fieldLabel: _('description')
                    ,name: 'description'
                    ,anchor: '100%'
                },{
                    xtype: 'xcheckbox'
                    ,fieldLabel:'Active'
                    ,name: 'active'
                    ,checked: true
                    ,anchor: '100%'
                },{
                    xtype: 'textfield'
                    ,name: 'position'
                    ,hidden: true
                }]
            },{
                layout:'form'
                ,columnWidth:.5
                ,items:[{
                    xtype: 'modx-combo-browser'
                    ,fieldLabel: 'Upload Image'
                    ,name: 'image'
                    ,id: 'location-image-field-'+Ext.id()
                    ,fixed:false
                    ,anchor:'100%'
                    ,rootId: '/'
                    ,openTo: '/'
                    ,listeners:{
                        'afterrender': function() {
                            if(config.record.id && this.getValue()) {
                                me.renderImage(this.ownerCt.getId(), this.getValue());
                            }
                        }
                        ,'select':function() {
                            me.renderImage(this.ownerCt.getId(), this.getValue());
                        }
                    }
                }]
            }]
        }]
    });
    modTimetable.window.Session.superclass.constructor.call(this,config);
};
Ext.extend(modTimetable.window.Session,MODx.Window,{
    renderImage:function(colId,path) {
        var rightCol = Ext.getCmp(colId);
        if(path.charAt(0) !== '/') {
            path = '/'+path;
        }
        rightCol.remove('school-image-preview-'+this.config.record.id);
        rightCol.add({
            html: '<img style="width:100%; margin-top:10px;" src="' + path + '" />'
            ,id: 'school-image-preview-'+this.config.record.id
        });
        rightCol.doLayout();
    }
});
Ext.reg('modtimetable-window-session',modTimetable.window.Session);

