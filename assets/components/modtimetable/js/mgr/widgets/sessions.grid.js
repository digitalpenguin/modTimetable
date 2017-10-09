modTimetable.grid.Sessions = function(config) {
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
        id: 'modtimetable-grid-sessions'
        ,url: modTimetable.config.connectorUrl
        ,baseParams: {
            action: 'mgr/session/getlist'
        }
        ,save_action: 'mgr/session/updatefromgrid'
        ,autosave: true
        ,fields: ['id','name','description','num_in_week','image','position']
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
            ,width:250
            ,fixed:true
            ,renderer: { fn: this.actionColumnRenderer, scope: this }
        },{
            header: _('modtimetable.session.description')
            ,dataIndex: 'description'
            ,width: 250
            ,editor: { xtype: 'textfield' }
        },{
            header: _('modtimetable.session.image')
            ,dataIndex: 'image'
            ,width: 150
            ,fixed:true
            ,editor: { xtype: 'textfield' }
        },{
            header: _('modtimetable.session.position')
            ,dataIndex: 'position'
            ,width: 50
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
            text:'<h2 style="font-size:22px; margin-top:2px; color:#aaa;">Sessions</h2>'
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
        createSession.fp.getForm().findField('timetableId').setValue(this.timetableId);
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
    Ext.applyIf(config,{
        title: _('modtimetable.session.create')
        ,closeAction: 'close'
        ,url: modTimetable.config.connectorUrl
        ,action: 'mgr/session/create'
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
    modTimetable.window.Session.superclass.constructor.call(this,config);
};
Ext.extend(modTimetable.window.Session,MODx.Window);
Ext.reg('modtimetable-window-session',modTimetable.window.Session);

