modTimetable.grid.Days = function(config) {
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
        id: 'modtimetable-grid-days'
        ,url: modTimetable.config.connectorUrl
        ,baseParams: {
            action: 'mgr/day/getlist'
        }
        ,save_action: 'mgr/day/updatefromgrid'
        ,autosave: true
        ,fields: ['id','name','description','image','active','position']
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
            header: _('modtimetable.day.active')
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
            header: _('modtimetable.day.image')
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
            header: _('modtimetable.day.position')
            ,dataIndex: 'position'
            ,width: 50
            ,editor: { xtype: 'numberfield', allowDecimal: false, allowNegative: false }
        }]
        ,tbar: [{
            text: '<i style="margin-right:3px;" class="icon icon-arrow-left"></i> '+_('modtimetable.day.backtotimetables')
            ,handler: this.backToTimetableGrid
            ,scope: this
        },{
            text: '<i style="margin-right:3px;" class="icon icon-plus"></i> '+_('modtimetable.day.create')
            ,handler: this.createDay
            ,scope: this
        },{
            xtype:'tbtext',
            text:'<h2 style="font-size:22px; margin-top:2px; color:#aaa;">'+config.timetableRec.data['name']+' '+_('modtimetable.day.timetable_days')+'</h2>'
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
                                    action: 'mgr/day/reorder'
                                    ,idItem: records.pop().id
                                    ,oldIndex: oldIndex
                                    ,newIndex: newIndex
                                    ,timetableId: config.timetableId
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
                case 'viewSessions':
                    this.loadSessionsCard(record, e);
                    break;
                default:
                    break;
            }
        }
    }

    ,loadSessionsCard: function(record, e) {
        var cards = Ext.getCmp('timetable-card-container');
        if(Ext.getCmp('modtimetable-grid-sessions')){
            Ext.getCmp('modtimetable-grid-sessions').destroy();
        }
        var sessionsPanel = Ext.getCmp('modtimetable-panel-sessions');
        var sessionsGrid = MODx.load({
            xtype: 'modtimetable-grid-sessions',
            dayId: record.data['id'],
            dayRecord: record,
            baseParams:{
                action: 'mgr/session/getlist',
                dayId:record.data['id']
            }
        });
        sessionsPanel.add(sessionsGrid);
        cards.getLayout().setActiveItem(2);
        this.updateBreadcrumbs( _('modtimetable.session.managesessions')+' '+record.data['name'],record);
        this.fireEvent('sessionspanelloaded',record);
    }

    // Makes this card the active one
    ,activate: function() {
        var cardLayout = Ext.getCmp('timetable-card-container').getLayout();
        var oldRecord = cardLayout.activeItem.record;
        cardLayout.setActiveItem(1);
        this.refresh();

        this.setCurrentBreadcrumb( _('modtimetable.day.managedays')+' '+ this.timetableRec.data['name'],this.timetableRec);
        cardLayout.activeItem.fireEvent('daysactivated',oldRecord);
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
            text : rec.data['name']
            ,rec: rec
        };
        bd.trail.push(newBcItem);
        bc.updateDetail(bd);
    }

    // Removes a breadcrumb and makes this one current
    ,setCurrentBreadcrumb: function(msg, rec){
        this.resetBreadcrumbs(msg);
        var bc = Ext.getCmp('timetable-breadcrumbs');
        var bd = bc.getData();
        bd.text = msg;

        bd.trail.shift();
        if (bd.trail.length > 0) {
            bd.trail[bd.trail.length - 1].install = true;
        }
        var newBcItem = {
            text : rec.data['name']
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
        }
        Ext.getCmp('timetable-breadcrumbs').reset(msg);
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
    var me = this;
    Ext.applyIf(config,{
        title: _('modtimetable.day.create')
        ,closeAction: 'close'
        ,width:650
        ,url: modTimetable.config.connectorUrl
        ,action: 'mgr/day/create'
        ,fields: [{
            layout: 'column'
            , items: [{
                layout: 'form'
                , columnWidth: .5
                , items: [{
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
                    xtype: 'xcheckbox'
                    ,fieldLabel: 'Active'
                    ,name: 'active'
                    ,checked:true
                    ,anchor: '100%'
                },{
                    xtype: 'textfield'
                    ,name: 'position'
                    ,hidden: true
                }]
            },{
                layout: 'form'
                ,columnWidth: .5
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
    modTimetable.window.Day.superclass.constructor.call(this,config);
};
Ext.extend(modTimetable.window.Day,MODx.Window, {
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
Ext.reg('modtimetable-window-day',modTimetable.window.Day);

