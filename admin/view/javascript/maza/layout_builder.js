// Grid Container initialisation
function gridContainerInteractions(container){
    
    // Sortable
    $(container).sortable({
        revert: true,
        items: "> .grid-root", 
        placeholder: "placeholder-allow",
        connectWith: ".layout-builder-section-content.content-sortable, .grid-col > .child-entry, .grid-section > .child-entry",
        over: function( event, ui ) {
            // Accept only allow grid
            if(ui.item.hasClass('grid-row') || ui.item.hasClass('grid-section')){
                ui.placeholder.removeClass('placeholder-error');
                ui.item.removeClass('not-allowed');
            } else {
                ui.placeholder.addClass('placeholder-error');
                ui.item.addClass('not-allowed');
            }
            
            // placeholder
            ui.placeholder.innerHeight(ui.item.innerHeight());
//            ui.placeholder.css('marginBottom', ui.item.css('marginBottom'));
            
//            if(ui.item.hasClass('grid-section')){
//                ui.placeholder.addClass('section-droppable-placeholder');
//            }
        },
        receive: function( event, ui ) {
            if(ui.item.hasClass('grid-row') || ui.item.hasClass('grid-section')){
                ui.item.addClass('grid-root');
                ui.item.closest('.layout-builder-section-content').trigger('rootRow.change');
            } else {
                ui.item.removeClass('not-allowed');
                ui.sender.sortable('cancel');
            }
        },
        remove: function( event, ui ) {
            if(ui.item.hasClass('grid-row')){
                ui.item.removeClass('grid-root');
                ui.item.closest('.layout-builder-section-content').trigger('rootRow.change');
            }
        }
    }).disableSelection();
}

/**
 * Make area to droppable for extension
 * */
function makeExtensionDroppable(area){
    $(area).droppable({
        accept: "#extension-panel .extension-pill",
        greedy: true,
        hoverClass: "extension-pill-drop-hover",
        drop: function( event, ui ) {
            var extension = ui.draggable.clone();
            extension.data(ui.draggable.data());
            
            // Device visibility
            extension.data({
                device_xs_hidden: 0,
                device_sm_hidden: 0,
                device_md_hidden: 0,
                device_lg_hidden: 0,
                device_xl_hidden: 0,
                
                device_xs_order: 1,
                device_sm_order: 1,
                device_md_order: 1,
                device_lg_order: 1,
                device_xl_order: 1
            });
            
            // Module
            if(ui.draggable.hasClass('module')){
                extension.find('.extension-label').text(extension.data('path'));
                extension.find('.action-cog').before(
                    '<a href="#" class="extension-pill-action action-remove" target="_blank"><i class="fa fa-times"></i></a> '
                    + '<a href="#" class="extension-pill-action action-hide" ><i class="fa fa-eye-slash"></i></a> '
                    + '<a href="#" class="extension-pill-action action-duplicate"><i class="fa fa-copy"></i></a> '
                );
                extension.find('.action-cog').after('<a href="#" class="extension-pill-action action-edit" target="_blank"><i class="fa fa-pencil"></i></a>');
            }
            // widget
            if(ui.draggable.hasClass('widget')){
                // Add require option to extension pill before to drop
                extension.append(
                    '<a href="#" class="extension-pill-action action-remove" target="_blank"><i class="fa fa-times"></i></a> '
                    + '<a href="#" class="extension-pill-action action-hide" ><i class="fa fa-eye-slash"></i></a> '
                    + '<a href="#" class="extension-pill-action action-duplicate"><i class="fa fa-copy"></i></a> '
                    + '<a href="#" class="extension-pill-action action-cog" target="_blank"><i class="fa fa-cog"></i></a> '
                    + '<a href="#" class="extension-pill-action action-edit" target="_blank"><i class="fa fa-pencil"></i></a>'
                );
            }
            // design
            if(ui.draggable.hasClass('design')){
                // Add require option to extension pill before to drop
                extension.append(
                    '<a href="#" class="extension-pill-action action-remove" target="_blank"><i class="fa fa-times"></i></a> '
                    + '<a href="#" class="extension-pill-action action-hide" ><i class="fa fa-eye-slash"></i></a> '
                    + '<a href="#" class="extension-pill-action action-duplicate"><i class="fa fa-copy"></i></a> '
                    + '<a href="#" class="extension-pill-action action-cog" target="_blank"><i class="fa fa-cog"></i></a> '
                    + '<a href="#" class="extension-pill-action action-edit" target="_blank"><i class="fa fa-pencil"></i></a>'
                );
            }
            // content
            if(ui.draggable.hasClass('content')){
                // Add require option to extension pill before to drop
                extension.append(
                    '<a href="#" class="extension-pill-action action-remove" target="_blank"><i class="fa fa-times"></i></a> '
                    + '<a href="#" class="extension-pill-action action-hide" ><i class="fa fa-eye-slash"></i></a> '
                    + '<a href="#" class="extension-pill-action action-duplicate"><i class="fa fa-copy"></i></a> '
                    + '<a href="#" class="extension-pill-action action-cog" target="_blank"><i class="fa fa-cog"></i></a> '
                    + '<a href="#" class="extension-pill-action action-edit" target="_blank"><i class="fa fa-pencil"></i></a>'
                );
            }
            
            // append extension pill to dropped area
            $(this).append(extension);
            
            // Trigger update child event
            $(this).parent('.entry').trigger('child.update');
            
            if(ui.draggable.hasClass('widget') || ui.draggable.hasClass('design')){
                // Open widget setting modal after drop
                extension.find('.action-cog').trigger('click');
            }
            
            if(ui.draggable.hasClass('content')){
                // Open content setting modal after drop
                extension.find('.action-cog').trigger('click');
            }
        }
    });
}

// Section Interactions
function gridSectionInteractions(section, descendant = false){
    // Drop extension
    makeExtensionDroppable($(section).children(".child-entry"));
    
    // Sortable
    $(section).children(".child-entry").sortable({
        revert: true, 
        items: "> .extension-pill, > .grid-row", 
        placeholder: "placeholder-allow",
        connectWith: ".grid-section > .child-entry, .grid-component > .child-entry, .grid-col > .child-entry, .layout-builder-section-content.content-sortable",
        over: function( event, ui ) {
            // Not allow other section to accept
            if(ui.item.hasClass('grid-section')) {
                ui.placeholder.addClass('placeholder-error');
                ui.item.addClass('not-allowed');
//                ui.placeholder.addClass('section-droppable-placeholder');
            } else {
                // Reset
                ui.placeholder.removeClass('placeholder-error');
                ui.item.removeClass('not-allowed');
            }
            
            // Set styles
            ui.placeholder.innerHeight(ui.item.innerHeight());
//            ui.placeholder.css('marginBottom', ui.item.css('marginBottom'));
//            if(ui.item.hasClass('grid-row')){
//                ui.placeholder.addClass('row-droppable-placeholder');
//            }
        },
        receive: function( event, ui ) {
            if(ui.item.hasClass('grid-section')){
                ui.item.removeClass('not-allowed');
                ui.sender.sortable('cancel');
            } else {
                ui.item.data('device_xs_order', 0).data('device_sm_order', 0)
                    .data('device_md_order', 0).data('device_lg_order', 0).data('device_xl_order', 0);
            }
        },
        update: function( event, ui ) {
            // Trigger child update event
            $(event.target).parent().trigger('child.update');
        },
        remove: function( event, ui ) {
            // Trigger child update event
            $(event.target).parent().trigger('child.update');
        }
    }).disableSelection();
    
    // Attach child update event
    $(section).on('child.update', function(){
//        var device = $('#layout-builder-content').data('device');
        
        // Update order according to dom position
//        $(this).children().children('.entry').each(function(i){
////            var order = Number(i) + 1;
//            $(this).data('device_' + device + '_order', 0);
////            $(this).children('.col-' + device + '-count').text(order);
//        });
    });
    
    // descendant section Interactions
    if(descendant && $(section).children(".child-entry").children(".grid-row").length){
        gridRowInteractions($(section).children(".child-entry").children(".grid-row"), true);
    }
}

// Section destroy Interactions
function gridSectionDestroyInteractions(section, descendant = false){
    // destroy droppable
    $(section).children(".child-entry").droppable('destroy');
    
    // destroy sortable
    $(section).children(".child-entry").sortable('destroy');
    
    // Remove child update event
    $(section).off('child.update');
    
    // descendant section destroy
    if(descendant && $(section).children(".child-entry").children(".grid-row").length){
        gridRowDestroyInteractions($(section).children(".child-entry").children(".grid-row"), true);
    }
}

// Component Interactions
function gridComponentInteractions(component, descendant = false){
    // Drop extension
    makeExtensionDroppable($(component).children(".child-entry"));
    
    // Sortable
    $(component).children(".child-entry").sortable({
        revert: true, 
        items: "> .extension-pill, > .grid-row", 
        placeholder: "placeholder-allow",
        connectWith: ".grid-section > .child-entry, .grid-component > .child-entry, .grid-col > .child-entry, .layout-builder-section-content.content-sortable",
        over: function( event, ui ) {
            // Not allow  section to accept
            if(ui.item.hasClass('grid-section')) {
                ui.placeholder.addClass('placeholder-error');
                ui.item.addClass('not-allowed');
//                ui.placeholder.addClass('section-droppable-placeholder');
            } else {
                // Reset
                ui.placeholder.removeClass('placeholder-error');
                ui.item.removeClass('not-allowed');
            }
            
            // Set styles
            ui.placeholder.innerHeight(ui.item.innerHeight());
//            ui.placeholder.css('marginBottom', ui.item.css('marginBottom'));
//            if(ui.item.hasClass('grid-row')){
//                ui.placeholder.addClass('row-droppable-placeholder');
//            }
        },
        receive: function( event, ui ) {
            if(ui.item.hasClass('grid-section')){
                ui.item.removeClass('not-allowed');
                ui.sender.sortable('cancel');
            } else {
                ui.item.data('device_xs_order', 0).data('device_sm_order', 0)
                    .data('device_md_order', 0).data('device_lg_order', 0).data('device_xl_order', 0);
            }
        },
        update: function( event, ui ) {
            // Trigger child update event
            $(event.target).parent().trigger('child.update');
        },
        remove: function( event, ui ) {
            // Trigger child update event
            $(event.target).parent().trigger('child.update');
        }
    }).disableSelection();
    
    // Attach child update event
    $(component).on('child.update', function(){
//        var device = $('#layout-builder-content').data('device');
        
        // Update order according to dom position
//        $(this).children().children('.entry').each(function(i){
////            var order = Number(i) + 1;
//            $(this).data('device_' + device + '_order', 0);
////            $(this).children('.col-' + device + '-count').text(order);
//        });
    });
    
    // descendant component Interactions
    if(descendant && $(component).children(".child-entry").children(".grid-row").length){
        gridRowInteractions($(component).children(".child-entry").children(".grid-row"), true);
    }
}

// Component destroy Interactions
function gridComponentDestroyInteractions(component, descendant = false){
    // destroy droppable
    $(component).children(".child-entry").droppable('destroy');
    
    // destroy sortable
    $(component).children(".child-entry").sortable('destroy');
    
    // Remove child update event
    $(component).off('child.update');
    
    // descendant component destroy
    if(descendant && $(component).children(".child-entry").children(".grid-row").length){
        gridRowDestroyInteractions($(component).children(".child-entry").children(".grid-row"), true);
    }
}

//Grid column initialisation
function gridColumnInteractions(col, descendant = false){
    // Drop extension
    makeExtensionDroppable($(col).children(".child-entry"));
    
    // Sortable
    $(col).children(".child-entry").sortable({
        revert: true, 
        items: "> .extension-pill, > .grid-row", 
        placeholder: "placeholder-allow",
        connectWith: ".grid-col > .child-entry, .grid-section > .child-entry, .grid-component > .child-entry, .layout-builder-section-content.content-sortable",
        over: function( event, ui ) {
            // Not allow  section to accept
            if(ui.item.hasClass('grid-section')) {
                ui.placeholder.addClass('placeholder-error');
                ui.item.addClass('not-allowed');
            } else {
                // Reset
                ui.placeholder.removeClass('placeholder-error');
                ui.item.removeClass('not-allowed');
            }
            
            // Set styles
            ui.placeholder.innerHeight(ui.item.innerHeight());
        },
        receive: function( event, ui ) {
            if(ui.item.hasClass('grid-section')){
                ui.item.removeClass('not-allowed');
                ui.sender.sortable('cancel');
            }
        },
        update: function( event, ui ) {
            // Trigger child update event
            $(event.target).parent().trigger('child.update');
        },
        remove: function( event, ui ) {
            // Trigger child update event
            $(event.target).parent().trigger('child.update');
        }
    }).disableSelection();
    
    // Resize
    $(col).resizable({
        handles: "e",
        resize: function( event, ui ) {
            var parent = ui.element.parent();
            var width_perc = ui.size.width/parent.width()*100;
            if(width_perc > 100){
                width_perc = 100;
            } else if(width_perc < 1){
                width_perc = 100 / 12;
            }
            
            // Get grid size based on current resize
            var grid_size = Math.ceil(width_perc / (100 / 12));
            
            // Get active device
            var device = $('#layout-builder-content').data('device');
            
            if(typeof last_grid_size === "undefined" || last_grid_size !== grid_size){
                ui.element.attr('class', ui.element.attr('class').replace(new RegExp('gc-' + device + '-\\d+', 'g'), 'gc-' + device + '-' + grid_size));
                ui.element.children('.col-' + device + '-count').text(grid_size + '/12');
                
                // Add grid data
                ui.element.data('device_' + device + '_size', grid_size);
                
                last_grid_size = grid_size;
            }
        }
    });
    
    // Attach child update event
    $(col).on('child.update', function(){
        var device = $('#layout-builder-content').data('device');
        
        // Update order according to dom position
        $(this).children().children('.entry:not(.gc-hidden-' + device + ')').each(function(i){
            var order = Number(i) + 1;
            $(this).data('device_' + device + '_order', order);
//            $(this).children('.col-' + device + '-count').text(order);
        });
    });
    
    // descendant row Interactions
    if(descendant && $(col).children(".child-entry").children(".grid-row").length){
        gridRowInteractions($(col).children(".child-entry").children(".grid-row"), true);
    }
    
}

// Column destroy Interactions
function gridColumnDestroyInteractions(col, descendant = false){
    // destroy droppable
    $(col).children(".child-entry").droppable('destroy');
    
    // destroy sortable
    $(col).children(".child-entry").sortable('destroy');
    
    // destroy resizable
    $(col).resizable('destroy');
    
    // Remove child update event
    $(col).off('child.update');
    
    // descendant row destroy
    if(descendant && $(col).children(".child-entry").children(".grid-row").length){
        gridRowDestroyInteractions($(col).children(".child-entry").children(".grid-row"), true);
    }
}

// Grid row initilisation
function gridRowInteractions(row, descendant = false){
    // sort column
    $(row).children(".child-entry").sortable({revert: true, 
        items: "> .grid-col", 
        placeholder: "col-droppable-placeholder",
        connectWith: ".grid-row > .child-entry",
        tolerance: "pointer",
        grid: [ 20, 10 ],
        appendTo: document.body,
        over: function( event, ui ) {
            var device = $('#layout-builder-content').data('device');
            
            // Style placeholder
            ui.placeholder.innerHeight(ui.item.innerHeight());
            ui.placeholder.addClass(ui.item.attr('class').match(new RegExp('gc-' + device + '-\\d+'))[0]);
           
        },
        update: function( event, ui ) {
            // Trigger child update event
            $(event.target).parent().trigger('child.update');
        },
        remove: function( event, ui ) {
            // Trigger child update event
            $(event.target).parent().trigger('child.update');
        }
    });
    
    // Attach child update event
    $(row).on('child.update', function(){
        var device = $('#layout-builder-content').data('device');
        
        // Update order according to dom position
        $(this).children().children('.entry:not(.gc-hidden-' + device + ')').each(function(i){
            var order = Number(i) + 1;
            $(this).data('device_' + device + '_order', order);
//            $(this).children('.col-' + device + '-count').text(order);
        });
    });
    
    // descendant row Interactions
    if(descendant && $(row).children(".child-entry").children(".grid-col").length){
        gridColumnInteractions($(row).children(".child-entry").children(".grid-col"), true);
    }
}

// Row destroy Interactions
function gridRowDestroyInteractions(row, descendant = false){
    // destroy sortable
    $(row).children(".child-entry").sortable('destroy');
    
    // Remove child update event
    $(row).off('child.update');
    
    // descendant Column destroy
    if(descendant && $(row).children(".child-entry").children(".grid-col").length){
        gridColumnDestroyInteractions($(row).children(".child-entry").children(".grid-col"), true);
    }
}

// Row structure
function createRow(root){
    var row;
    
    if(root){
        row = '<div class="grid-row entry row grid-root"><div class="child-entry row"><span class="empty-placeholder"></span></div></div>';
    } else {
        row = '<div class="grid-row entry row"><div class="child-entry row"><span class="empty-placeholder"></span></div></div>';
    }
    
    row = $(row);
    
    // set default device data
    row.data({
        device_xs_hidden: 0,
        device_sm_hidden: 0,
        device_md_hidden: 0,
        device_lg_hidden: 0,
        device_xl_hidden: 0,
        
        device_xs_order: 1,
        device_sm_order: 1,
        device_md_order: 1,
        device_lg_order: 1,
        device_xl_order: 1
    });
    
    // Create drag and drop
    gridRowInteractions(row);
    
    return row;
}

// Section structure
function createSection(){
    var section;
    
    section = '<div class="grid-section entry section grid-root"><div class="child-entry section"><span class="empty-placeholder"></span></div></div>';
    
    section = $(section);
    
    // set default device data
    section.data({
        device_xs_hidden: 0,
        device_sm_hidden: 0,
        device_md_hidden: 0,
        device_lg_hidden: 0,
        device_xl_hidden: 0
    });
    
    // Create drag and drop
    gridSectionInteractions(section);
    
    return section;
}

// Component structure
function createComponent(){
    var component;
    
    component = '<div class="grid-component entry component grid-root"><div class="child-entry component"><span class="empty-placeholder"></span></div></div>';
    
    component = $(component);
    
    // set default device data
    component.data({
        device_xs_hidden: 0,
        device_sm_hidden: 0,
        device_md_hidden: 0,
        device_lg_hidden: 0,
        device_xl_hidden: 0
    });
    
    // Create drag and drop
    gridComponentInteractions(component);
    
    return component;
}

// Column structure
function createColumn(device){
    var col;
    
    // Grid size
    var col_class = 'gc-xs-' + device.xs.size + ' gc-sm-' + device.sm.size + ' gc-md-' + device.md.size + ' gc-lg-' + device.lg.size + ' gc-xl-' + device.xl.size;
    
    // Grid hidden
    col_class += (device.xs.hidden)?' gc-hidden-xs':'';
    col_class += (device.sm.hidden)?' gc-hidden-sm':'';
    col_class += (device.md.hidden)?' gc-hidden-md':'';
    col_class += (device.lg.hidden)?' gc-hidden-lg':'';
    col_class += (device.xl.hidden)?' gc-hidden-xl':'';
    
    col = '<div class="' + col_class + ' grid-col entry">';
    col += '<span class="col-xs-count">' + device.xs.size + '/12</span>';
    col += '<span class="col-sm-count">' + device.sm.size + '/12</span>';
    col += '<span class="col-md-count">' + device.md.size + '/12</span>';
    col += '<span class="col-lg-count">' + device.lg.size + '/12</span>';
    col += '<span class="col-xl-count">' + device.xl.size + '/12</span>';
    col += '<div class="child-entry"><span class="empty-placeholder"></span></div>';
    col += '</div>';
    
    col = $(col);
    
    // set default device data
    col.data({
        device_xs_hidden: device.xs.hidden,
        device_sm_hidden: device.sm.hidden,
        device_md_hidden: device.md.hidden,
        device_lg_hidden: device.lg.hidden,
        device_xl_hidden: device.xl.hidden,
        device_xs_size: device.xs.size,
        device_sm_size: device.sm.size,
        device_md_size: device.md.size,
        device_lg_size: device.lg.size,
        device_xl_size: device.xl.size,
        device_xs_order: device.xs.order,
        device_sm_order: device.sm.order,
        device_md_order: device.md.order,
        device_lg_order: device.lg.order,
        device_xl_order: device.xl.order
    });
    
    gridColumnInteractions(col);
    
    return col;
}

// Layout attached
function layout(layout_id){
    var layout = $(layout_id);
    var layout_content = layout.find('.layout-builder-section-content');
    
    // Add section
    layout.find('.section-plus').on('click', function(){
        
        // Create and add section
        var section = createSection(true);
        layout_content.append(section); 
        
        // trigger event
        layout_content.trigger('rootSection.change');
    });
    
    // remove section
    layout.find('.section-minus').on('click', function(){
        layout_content.children('.grid-section:last-child').remove();
        
        // trigger event
        layout_content.trigger('rootSection.change');
    });
    
    layout_content.on('rootSection.change', function(){
        // Count root section
        layout.find('.section-count').text(layout_content.children('.section.grid-root').length);
    });
    
    
    // Add component
    layout.find('.component-plus').on('click', function(){
        
        // Create and add component
        var component = createComponent(true);
        layout_content.append(component);
        component.click();
        $('#edit-component').modal('show');
        
        // trigger event
        layout_content.trigger('rootComponent.change');
    });
    
    // remove component
    layout.find('.component-minus').on('click', function(){
        layout_content.children('.grid-component:last-child').remove();
        
        // trigger event
        layout_content.trigger('rootComponent.change');
    })
    
    layout_content.on('rootComponent.change', function(){
        // Count root component
        layout.find('.component-count').text(layout_content.children('.component.grid-root').length);
    });
    
    
    // Add row
    layout.find('.row-plus').on('click', function(){
        
        // Create and add row with column, 
        // every row have at least one column requeried
        var row = createRow(true);
        row.children('.child-entry').append(createColumn({
            xs: {size: 12, hidden: 0, order: 1},
            sm: {size: 12, hidden: 0, order: 1},
            md: {size: 12, hidden: 0, order: 1},
            lg: {size: 12, hidden: 0, order: 1},
            xl: {size: 12, hidden: 0, order: 1}
        }));
        layout_content.append(row); 
        
        // trigger event
        layout_content.trigger('rootRow.change');
    })
    
    // remove row
    layout.find('.row-minus').on('click', function(){
        layout_content.children('.grid-row:last-child').remove();
        
        // trigger event
        layout_content.trigger('rootRow.change');
    })
    
    layout_content.on('rootRow.change', function(){
        // Count root row
        layout.find('.row-count').text(layout_content.children('.row.grid-root').length);
    });
    
    // Visibility toggle
    layout.find('.visibility-toggle').on('click', function(){
            if($(this).data('show')){
                $(this).data('show', 0);
                layout.removeClass('show-hidden');
                $(this).html('<i class="fa fa-eye-slash" aria-hidden="true"></i>');
            } else {
                $(this).data('show', 1);
                layout.addClass('show-hidden');
                $(this).html('<i class="fa fa-eye" aria-hidden="true"></i>');
            }
    });
    
    // Set Interactions for drag and drop
    if(layout_content.hasClass('content-sortable')){
        gridContainerInteractions(layout_content);
    }
}

// serialize layout table table
function serializeTable(table){
    return serializeEntry($(table).find('.grid-root'));
}

// serialize table entry
function serializeEntry(entry){
    var data = [];
    
    entry.each(function(i){
        var entry_data = {};
        
        entry_data['setting'] = $(this).data('setting');
        
        var is_orderable = false; // Type is orderable or not
        
        // Section entry
        if($(this).hasClass('grid-section')){
            entry_data['type'] = 'section';
        }
        
        // Component entry
        if($(this).hasClass('grid-component')){
            entry_data['type'] = 'component';
        }
        
        // Row entry
        if($(this).hasClass('grid-row')){
            entry_data['type'] = 'row';
            is_orderable = true;
        }
        
        // Column entry
        if($(this).hasClass('grid-col')){
            entry_data['type'] = 'col';
            
            // Device profile
            entry_data['device_size'] = {
                xs: Number($(this).data('device_xs_size')),
                sm: Number($(this).data('device_sm_size')),
                md: Number($(this).data('device_md_size')),
                lg: Number($(this).data('device_lg_size')),
                xl: Number($(this).data('device_xl_size'))
            };
            
            is_orderable = true;
        }
        
        // Module entry
        if($(this).hasClass('module')){
            entry_data['type'] = 'module';
            entry_data['code'] = $(this).data('code');
            is_orderable = true;
        }
        
        // Widget entry
        if($(this).hasClass('widget')){
            entry_data['type'] = 'widget';
            entry_data['code'] = $(this).data('code');
            entry_data['widget_data'] = $(this).data('widget_data');
            is_orderable = true;
        }
        
        // Design entry
        if($(this).hasClass('design')){
            entry_data['type'] = 'design';
            entry_data['code'] = $(this).data('code');
            entry_data['design_data'] = $(this).data('design_data');
            is_orderable = true;
        }
        
        // Content entry
        if($(this).hasClass('content')){
            entry_data['type'] = 'content';
            entry_data['code'] = $(this).data('code');
            entry_data['content_data'] = $(this).data('content_data');
            is_orderable = true;
        }
        
        // Menu entry
        if($(this).hasClass('menu')){
            entry_data['type'] = 'menu';
            entry_data['code'] = $(this).data('code');
            is_orderable = true;
        }
        
        // Device hidden
        entry_data['device_hidden'] = {
            xs: Number($(this).data('device_xs_hidden')),
            sm: Number($(this).data('device_sm_hidden')),
            md: Number($(this).data('device_md_hidden')),
            lg: Number($(this).data('device_lg_hidden')),
            xl: Number($(this).data('device_xl_hidden'))
        };
        if(entry_data['device_hidden']['xs'] == 1 && entry_data['device_hidden']['sm'] == 1 && entry_data['device_hidden']['md'] == 1 && entry_data['device_hidden']['lg'] == 1 && entry_data['device_hidden']['xl'] == 1){
            entry_data = false;
        }
        
        // Root element can not be orderable
        if($(this).hasClass('grid-root')){ 
            is_orderable = false;
        }
        
        // Set device order
        if(is_orderable){
            var xs_order = $(this).data('device_xs_order');
            var sm_order = $(this).data('device_sm_order');
            var md_order = $(this).data('device_md_order');
            var lg_order = $(this).data('device_lg_order');
            var xl_order = $(this).data('device_xl_order');
            
            entry_data['device_order'] = {
                xs: Number(isNaN(xs_order)?false:xs_order),
                sm: Number(isNaN(sm_order)?false:sm_order),
                md: Number(isNaN(md_order)?false:md_order),
                lg: Number(isNaN(lg_order)?false:lg_order),
                xl: Number(isNaN(xl_order)?false:xl_order)
            };
        }
            
        
        // Child entry
        if(entry_data && ($(this).children('.child-entry').children('.entry').length > 0)){
            entry_data['child_entry'] = serializeEntry($(this).children('.child-entry').children('.entry'));
        }
        
        if(entry_data){
            data[i] = entry_data;
        }
        
    });
    
    return data;
}

$(document).ready(function(){
        // layout responsive limit
        $('#layout-responsive-limit').on('input', function(){
            // Set min width of container
            $('#container').css('min-width', $(window).width() + ($(window).width() * $(this).val() /100));
        });

        $('#layout-responsive-limit').on('focus', function(){
            $(this).addClass('active');
        });
        $('#layout-responsive-limit').on('blur', function(e){
            $(this).removeClass('active');
        });

        // Drag extension
        $('#extension-panel .extension-pill').draggable({ revert: 'invalid', helper: "clone", scroll: true});

        // Column Interactions
        gridColumnInteractions($('.grid-col'));

        // Row Interactions
        gridRowInteractions($('.grid-row'));
        
        // Section Interactions
        gridSectionInteractions($('.grid-section'));
        
        // Component Interactions
        gridComponentInteractions($('.grid-component'));
        
        // Add row
        $('#button-add-row').on('click', function(){
            var add_row_setting = $('#add-row-setting');

            var device_data = {
                xs: {
                    size: add_row_setting.find('select[name="add_row_mobile_size"]').val(),
                    order: 1,
                    hidden: !Number(add_row_setting.find('input[name="add_row_mobile_visibility"]:checked').val())
                },
                sm: {
                    size: add_row_setting.find('select[name="add_row_tablet_size"]').val(),
                    order: 1,
                    hidden: !Number(add_row_setting.find('input[name="add_row_tablet_visibility"]:checked').val())
                },
                md: {
                    size: add_row_setting.find('select[name="add_row_tablet2_size"]').val(),
                    order: 1,
                    hidden: !Number(add_row_setting.find('input[name="add_row_tablet2_visibility"]:checked').val())
                },
                lg: {
                    size: add_row_setting.find('select[name="add_row_laptop_size"]').val(),
                    order: 1,
                    hidden: !Number(add_row_setting.find('input[name="add_row_laptop_visibility"]:checked').val())
                },
                xl: {
                    size: add_row_setting.find('select[name="add_row_desktop_size"]').val(),
                    order: 1,
                    hidden: !Number(add_row_setting.find('input[name="add_row_desktop_visibility"]:checked').val())
                }
            }

            var row = createRow();
            row.children('.child-entry').html(createColumn(device_data));

            add_row_setting.data('add_to_instance').children('.child-entry').append(row);
            
            // Trigger update child event
            add_row_setting.data('add_to_instance').trigger('child.update');
        });
        
        // Add column
        $('#button-add-column').on('click', function(){
            var add_column_setting = $('#add-column-setting');

            var device_data = {
                xs: {
                    size: add_column_setting.find('select[name="add_column_mobile_size"]').val(),
                    order: 1,
                    hidden: !Number(add_column_setting.find('input[name="add_column_mobile_visibility"]:checked').val())
                },
                sm: {
                    size: add_column_setting.find('select[name="add_column_tablet_size"]').val(),
                    order: 1,
                    hidden: !Number(add_column_setting.find('input[name="add_column_tablet_visibility"]:checked').val())
                },
                md: {
                    size: add_column_setting.find('select[name="add_column_tablet2_size"]').val(),
                    order: 1,
                    hidden: !Number(add_column_setting.find('input[name="add_column_tablet2_visibility"]:checked').val())
                },
                lg: {
                    size: add_column_setting.find('select[name="add_column_laptop_size"]').val(),
                    order: 1,
                    hidden: !Number(add_column_setting.find('input[name="add_column_laptop_visibility"]:checked').val())
                },
                xl: {
                    size: add_column_setting.find('select[name="add_column_desktop_size"]').val(),
                    order: 1,
                    hidden: !Number(add_column_setting.find('input[name="add_column_desktop_visibility"]:checked').val())
                }
            }

            add_column_setting.data('add_to_instance').children('.child-entry').append(createColumn(device_data));
            
            // Trigger update child event
            add_column_setting.data('add_to_instance').trigger('child.update');
        });
        
        /*** Section Action ***/
        // Section select
        $('#layout-builder-content').delegate('.grid-section' , 'click', function(e){
            e.stopPropagation();
            
            // set data
            $('#grid-section-action').data('instance', $(this));
            $('#edit-section').data('section', $(this));
            $('#add-row-setting').data('add_to_instance', $(this));
            
            // active element
            $('.grid-section.active').removeClass('active');
            $(this).addClass('active');
            
            // open action panel
            if(!$('#grid-section-action').hasClass('in')){
                $('#grid-action .collapse').collapse("hide");
                $('#grid-section-action').collapse("show");
            }
            
            $('#grid-section-action').trigger('section.change');
        });

        // Delete section
        $('#grid-section-action .btn-delete').on('click', function(){
            $('#grid-section-action').data('instance').remove();
            $('#grid-section-action').collapse('hide');
        });

        // hide section
        $('#grid-section-action .btn-hide').on('click', function(){
            var section = $('#grid-section-action').data('instance');
            
            if(section.data('device_' + $('#layout-builder-content').data('device') + '_hidden')){
                section.removeClass('gc-hidden-' + $('#layout-builder-content').data('device'));
                section.data('device_' + $('#layout-builder-content').data('device') + '_hidden', 0);
                $(this).html('<i class="fa fa-eye-slash" aria-hidden="true"></i> ' + $(this).data('text_hide'));
            } else {
                section.addClass('gc-hidden-' + $('#layout-builder-content').data('device'));
                section.data('device_' + $('#layout-builder-content').data('device') + '_hidden', 1);
                $(this).html('<i class="fa fa-eye" aria-hidden="true"></i> ' + $(this).data('text_show'));
                
                // Hide collapse if hidden element is not visible
                if(!section.closest('.layout-builder-section').hasClass('show-hidden')){
                    $('#grid-section-action').collapse('hide');
                }
            }
        });

        // duplicate section
        $('#grid-section-action .btn-duplicate').on('click', function(){
            var orignal_section = $('#grid-section-action').data('instance');
            gridSectionDestroyInteractions(orignal_section, true);
            
            var duplicate_section = orignal_section.clone(true).removeClass('active');
            orignal_section.after(duplicate_section);
            
            gridSectionInteractions(orignal_section, true);
            gridSectionInteractions(duplicate_section, true);

            // Trigger event
            if(duplicate_section.hasClass('grid-root')){
                duplicate_section.closest('.layout-builder-section-content').trigger('rootSection.change');
            }
        });
        
        /*** Component Action ***/
        // Component select
        $('#layout-builder-content').delegate('.grid-component' , 'click', function(e){
            e.stopPropagation();
            
            // set data
            $('#grid-component-action').data('instance', $(this));
            $('#edit-component').data('component', $(this));
            $('#add-row-setting').data('add_to_instance', $(this));
            
            // active element
            $('.grid-component.active').removeClass('active');
            $(this).addClass('active');
            
            // open action panel
            if(!$('#grid-component-action').hasClass('in')){
                $('#grid-action .collapse').collapse("hide");
                $('#grid-component-action').collapse("show");
            }
            
            $('#grid-component-action').trigger('component.change');
        });

        // Delete component
        $('#grid-component-action .btn-delete').on('click', function(){
            $('#grid-component-action').data('instance').remove();
            $('#grid-component-action').collapse('hide');
        });
        
        // hide component
        $('#grid-component-action .btn-hide').on('click', function(){
            var component = $('#grid-component-action').data('instance');
            
            if(component.data('device_' + $('#layout-builder-content').data('device') + '_hidden')){
                component.removeClass('gc-hidden-' + $('#layout-builder-content').data('device'));
                component.data('device_' + $('#layout-builder-content').data('device') + '_hidden', 0);
                $(this).html('<i class="fa fa-eye-slash" aria-hidden="true"></i> ' + $(this).data('text_hide'));
            } else {
                component.addClass('gc-hidden-' + $('#layout-builder-content').data('device'));
                component.data('device_' + $('#layout-builder-content').data('device') + '_hidden', 1);
                $(this).html('<i class="fa fa-eye" aria-hidden="true"></i> ' + $(this).data('text_show'));
                
                // Hide collapse if hidden element is not visible
                if(!component.closest('.layout-builder-section').hasClass('show-hidden')){
                    $('#grid-component-action').collapse('hide');
                }
            }
        });

        // duplicate component
        $('#grid-component-action .btn-duplicate').on('click', function(){
            var orignal_component = $('#grid-component-action').data('instance');
            gridComponentDestroyInteractions(orignal_component, true);
            
            var duplicate_component = orignal_component.clone(true).removeClass('active');
            orignal_component.after(duplicate_component);
            
            gridComponentInteractions(orignal_component, true);
            gridComponentInteractions(duplicate_component, true);

            // Trigger event
            if(duplicate_component.hasClass('grid-root')){
                duplicate_component.closest('.layout-builder-section-content').trigger('rootComponent.change');
            }
        });

        /*** Row Action ***/
        // Row select
        $('#layout-builder-content').delegate('.grid-row' , 'click', function(e){
            e.stopPropagation();
            // set data
            $('#grid-row-action').data('instance', $(this));
            $('#edit-row').data('row', $(this));
            $('#add-column-setting').data('add_to_instance', $(this));
            
            // active element
            $('.grid-row.active').removeClass('active');
            $(this).addClass('active');
            
            // open action panel
            if(!$('#grid-row-action').hasClass('in')){
                $('#grid-action .collapse').collapse("hide");
                $('#grid-row-action').collapse("show");
            }
            
            $('#grid-row-action').trigger('row.change');
        });

        // Delete row
        $('#grid-row-action .btn-delete').on('click', function(){
            var parent_entry = $('#grid-row-action').data('instance').closest('.entry');
            
            $('#grid-row-action').data('instance').remove();
            $('#grid-row-action').collapse('hide');
            
            // Trigger child update event
            parent_entry.trigger('child.update');
        });

        // hide row
        $('#grid-row-action .btn-hide').on('click', function(){
            var row = $('#grid-row-action').data('instance');
            
            if(row.data('device_' + $('#layout-builder-content').data('device') + '_hidden')){
                row.removeClass('gc-hidden-' + $('#layout-builder-content').data('device'));
                row.data('device_' + $('#layout-builder-content').data('device') + '_hidden', 0);
                $(this).html('<i class="fa fa-eye-slash" aria-hidden="true"></i> ' + $(this).data('text_hide'));
            } else {
                row.addClass('gc-hidden-' + $('#layout-builder-content').data('device'));
                row.data('device_' + $('#layout-builder-content').data('device') + '_hidden', 1);
                $(this).html('<i class="fa fa-eye" aria-hidden="true"></i> ' + $(this).data('text_show'));
                
                // Hide collapse if hidden element is not visible
                if(!row.closest('.layout-builder-section').hasClass('show-hidden')){
                    $('#grid-row-action').collapse('hide');
                }
            }
            
            row.closest('.entry').trigger('child.update');
        });

        // duplicate row
        $('#grid-row-action .btn-duplicate').on('click', function(){
            var orignal_row = $('#grid-row-action').data('instance');
            gridRowDestroyInteractions(orignal_row, true);
            
            var duplicate_row = orignal_row.clone(true).removeClass('active');
            orignal_row.after(duplicate_row);
            
            gridRowInteractions(orignal_row, true);
            gridRowInteractions(duplicate_row, true);
            
            // Trigger child update
            duplicate_row.closest('.entry').trigger('child.update');

            // Trigger event
            if(duplicate_row.hasClass('grid-root')){
                duplicate_row.closest('.layout-builder-section-content').trigger('rootRow.change');
            }
        });



        /*** Column Action ***/
        $('#layout-builder-content').delegate('.grid-col', 'click', function(e){
            e.stopPropagation();
            // set data
            $('#grid-col-action').data('instance', $(this));
            $('#edit-col').data('col', $(this));
            $('#add-row-setting').data('add_to_instance', $(this));
            
            // active element
            $('.grid-col.active').removeClass('active');
            $(this).addClass('active');
            
            // open action panel
            if(!$('#grid-col-action').hasClass('in')){
                $('#grid-action .collapse').collapse("hide");
                $('#grid-col-action').collapse("show");
            }
            
            $('#grid-col-action').trigger('column.change');
        });

        // Delete column
        $('#grid-col-action .btn-delete').on('click', function(){
            var parent_entry = $('#grid-col-action').data('instance').closest('.entry');
            
            // Delete column instance
            $('#grid-col-action').data('instance').remove();
            
            // Trigger child update event
            parent_entry.trigger('child.update');
            
            $('#grid-col-action').collapse('hide');
        });

        // hide col
        $('#grid-col-action .btn-hide').on('click', function(){
            var col = $('#grid-col-action').data('instance');
            
            if(col.data('device_' + $('#layout-builder-content').data('device') + '_hidden')){
                col.removeClass('gc-hidden-' + $('#layout-builder-content').data('device'));
                col.data('device_' + $('#layout-builder-content').data('device') + '_hidden', 0);
                $(this).html('<i class="fa fa-eye-slash" aria-hidden="true"></i> ' + $(this).data('text_hide'));
            } else {
                col.addClass('gc-hidden-' + $('#layout-builder-content').data('device'));
                col.data('device_' + $('#layout-builder-content').data('device') + '_hidden', 1);
                $(this).html('<i class="fa fa-eye" aria-hidden="true"></i> ' + $(this).data('text_show'));
                
                // Hide collapse if hidden element is not visible
                if(!col.closest('.layout-builder-section').hasClass('show-hidden')){
                    $('#grid-col-action').collapse('hide');
                }
            }
            
            // Trigger row child column update event
            col.closest('.entry').trigger('child.update');
        });

        // duplicate col
        $('#grid-col-action .btn-duplicate').on('click', function(){
            var orignal_col = $('#grid-col-action').data('instance');
            gridColumnDestroyInteractions(orignal_col, true);
            
            var duplicate_col = orignal_col.clone(true).removeClass('active');
            orignal_col.after(duplicate_col);
            
            var row = duplicate_col.closest('.grid-row');
            
            gridColumnInteractions(orignal_col, true);
            gridColumnInteractions(duplicate_col, true);
            gridRowDestroyInteractions(row);
            gridRowInteractions(row);
            
            // Trigger child update
            row.trigger('child.update');
        });


        // grid action panel
        $("#grid-section-action").on('hide.bs.collapse', function(){
            $('.grid-section.active').removeClass('active');
        });
        $("#grid-component-action").on('hide.bs.collapse', function(){
            $('.grid-component.active').removeClass('active');
        });
        $("#grid-row-action").on('hide.bs.collapse', function(){
            $('.grid-row.active').removeClass('active');
        });
        $("#grid-col-action").on('hide.bs.collapse', function(){
            $('.grid-col.active').removeClass('active');
        });
        
        // Change hide button status
        $("#grid-section-action").on('section.change', function(){
            var section = $(this).data('instance');
            
            if(section.data('device_' + $('#layout-builder-content').data('device') + '_hidden')){
                $(this).find('.btn-hide').html('<i class="fa fa-eye" aria-hidden="true"></i> ' + $(this).find('.btn-hide').data('text_show'));
            } else {
                $(this).find('.btn-hide').html('<i class="fa fa-eye-slash" aria-hidden="true"></i> ' + $(this).find('.btn-hide').data('text_hide'));
            }
        });
        $("#grid-component-action").on('component.change', function(){
            var component = $(this).data('instance');
            
            if(component.data('device_' + $('#layout-builder-content').data('device') + '_hidden')){
                $(this).find('.btn-hide').html('<i class="fa fa-eye" aria-hidden="true"></i> ' + $(this).find('.btn-hide').data('text_show'));
            } else {
                $(this).find('.btn-hide').html('<i class="fa fa-eye-slash" aria-hidden="true"></i> ' + $(this).find('.btn-hide').data('text_hide'));
            }
        });
        $("#grid-row-action").on('row.change', function(){
            var row = $(this).data('instance');
            
            if(row.data('device_' + $('#layout-builder-content').data('device') + '_hidden')){
                $(this).find('.btn-hide').html('<i class="fa fa-eye" aria-hidden="true"></i> ' + $(this).find('.btn-hide').data('text_show'));
            } else {
                $(this).find('.btn-hide').html('<i class="fa fa-eye-slash" aria-hidden="true"></i> ' + $(this).find('.btn-hide').data('text_hide'));
            }
        });
        $("#grid-col-action").on('column.change', function(){
            var col = $(this).data('instance');
            
            if(col.data('device_' + $('#layout-builder-content').data('device') + '_hidden')){
                $(this).find('.btn-hide').html('<i class="fa fa-eye" aria-hidden="true"></i> ' + $(this).find('.btn-hide').data('text_show'));
            } else {
                $(this).find('.btn-hide').html('<i class="fa fa-eye-slash" aria-hidden="true"></i> ' + $(this).find('.btn-hide').data('text_hide'));
            }
        });
        
        // On device change
        $('.layout-builder-header [data-toggle="device-switch"]').on('device.change', function(){
            
            var device = $('#layout-builder-content').data('device');
            var section_action = $("#grid-section-action");
            var component_action = $("#grid-component-action");
            var row_action = $("#grid-row-action");
            var col_action = $("#grid-col-action");
            
            // change hide button of section
            if(section_action.hasClass('in')){
                var section = section_action.data('instance');
            
                if(section.data('device_' + device + '_hidden')){
                    section_action.find('.btn-hide').html('<i class="fa fa-eye" aria-hidden="true"></i> ' + section_action.find('.btn-hide').data('text_show'));
                } else {
                    section_action.find('.btn-hide').html('<i class="fa fa-eye-slash" aria-hidden="true"></i> ' + section_action.find('.btn-hide').data('text_hide'));
                }
            }
            
            // change hide button of component
            if(component_action.hasClass('in')){
                var component = component_action.data('instance');
            
                if(component.data('device_' + device + '_hidden')){
                    component_action.find('.btn-hide').html('<i class="fa fa-eye" aria-hidden="true"></i> ' + component_action.find('.btn-hide').data('text_show'));
                } else {
                    component_action.find('.btn-hide').html('<i class="fa fa-eye-slash" aria-hidden="true"></i> ' + component_action.find('.btn-hide').data('text_hide'));
                }
            }
            
            // change hide button of row
            if(row_action.hasClass('in')){
                var row = row_action.data('instance');
            
                if(row.data('device_' + device + '_hidden')){
                    row_action.find('.btn-hide').html('<i class="fa fa-eye" aria-hidden="true"></i> ' + row_action.find('.btn-hide').data('text_show'));
                } else {
                    row_action.find('.btn-hide').html('<i class="fa fa-eye-slash" aria-hidden="true"></i> ' + row_action.find('.btn-hide').data('text_hide'));
                }
            }
            
            // Change hide button of column
            if(col_action.hasClass('in')){
                var col = col_action.data('instance');
            
                if(col.data('device_' + device + '_hidden')){
                    col_action.find('.btn-hide').html('<i class="fa fa-eye" aria-hidden="true"></i> ' + col_action.find('.btn-hide').data('text_show'));
                } else {
                    col_action.find('.btn-hide').html('<i class="fa fa-eye-slash" aria-hidden="true"></i> ' + col_action.find('.btn-hide').data('text_hide'));
                }
            }
            
            // Sort DOM position of child based on order
            // Change log - .grid-section, .grid-component removed
            $('.grid-row, .grid-col').each(function(){
//                if(!$(this).hasClass('grid-root')){
                   $(this).children('.child-entry').children('.entry').sort(function(a, b){
                       var order_a = parseInt($(a).data('device_' + device + '_order'));
                       var order_b = parseInt($(b).data('device_' + device + '_order'));
                       
                       return order_a == order_b?0:(order_a > order_b?1:-1);
                    }).appendTo($(this).children('.child-entry'));

                    // Trigger child change event
                    $(this).trigger('child.update'); 
//                }
            });
            
        });
        $('.layout-builder-header .active>[data-toggle="device-switch"]').trigger('device.change');
        
        // Reset selection of modal open
        $('.edit-modal').each(function(){
            $(this).on('shown.bs.modal', function(event){
                $(event.target).find('.nav-pills').each(function(){
                    $('this').find('a:first').tab('show');
                });
            });
        });
        
        // Edit type action
        $('.select-edit-type').each(function(){
            $(this).find('a').on('show.bs.tab', function(event){
                if($(event.target).attr('href').search("style") >= 0){
                    $(event.target).parents('.modal-footer').find('.select-device').removeClass('hide');
                } else {
                    $(event.target).parents('.modal-footer').find('.select-device').addClass('hide');
                }
            })
        });

        /*** Module Action ***/

        // Duplicate module
        $('#layout-builder-content').delegate('.module .action-duplicate', 'click', function(e){
            e.preventDefault();
            e.stopPropagation();
            var original_module = $(this).closest('.module');
            original_module.after(original_module.clone().data(original_module.data()));
            
            // Trigger child change event for parent
            original_module.closest('.entry').trigger('child.update');
        });

        // remove module
        $('#layout-builder-content').delegate('.module .action-remove', 'click', function(e){
            e.preventDefault();
            e.stopPropagation();
            var parent_entry = $(this).closest('.module').closest('.entry');
            $(this).closest('.module').remove();
            
            // Trigger child change event for parent
            parent_entry.trigger('child.update');
        });
        
        // hide module
        $('#layout-builder-content').delegate('.module .action-hide', 'click', function(e){
            e.preventDefault();
            e.stopPropagation();
            var module = $(this).closest('.module');
            
            if(module.data('device_' + $('#layout-builder-content').data('device') + '_hidden')){
                module.removeClass('gc-hidden-' + $('#layout-builder-content').data('device'));
                module.data('device_' + $('#layout-builder-content').data('device') + '_hidden', 0);
                $(this).html('<i class="fa fa-eye-slash" aria-hidden="true"></i>');
            } else {
                module.addClass('gc-hidden-' + $('#layout-builder-content').data('device'));
                module.data('device_' + $('#layout-builder-content').data('device') + '_hidden', 1);
                $(this).html('<i class="fa fa-eye" aria-hidden="true"></i>');
            }
            
            // Trigger child change event for parent
            module.closest('.entry').trigger('child.update');
        });
        
        /*** Widget Action ***/
        
        // Duplicate widget
        $('#layout-builder-content').delegate('.widget .action-duplicate', 'click', function(e){
            e.preventDefault();
            e.stopPropagation();
            var original_widget = $(this).closest('.widget');
            original_widget.after(original_widget.clone().data(original_widget.data()));
            
            // Trigger child change event for parent
            original_widget.closest('.entry').trigger('child.update');
        });
        
        // hide widget
        $('#layout-builder-content').delegate('.widget .action-hide', 'click', function(e){
            e.preventDefault();
            e.stopPropagation();
            var widget = $(this).closest('.widget');
            
            if(widget.data('device_' + $('#layout-builder-content').data('device') + '_hidden')){
                widget.removeClass('gc-hidden-' + $('#layout-builder-content').data('device'));
                widget.data('device_' + $('#layout-builder-content').data('device') + '_hidden', 0);
                $(this).html('<i class="fa fa-eye-slash" aria-hidden="true"></i>');
            } else {
                widget.addClass('gc-hidden-' + $('#layout-builder-content').data('device'));
                widget.data('device_' + $('#layout-builder-content').data('device') + '_hidden', 1);
                $(this).html('<i class="fa fa-eye" aria-hidden="true"></i>');
            }
            
            // Trigger child change event for parent
            widget.closest('.entry').trigger('child.update');
        });

        // remove widget
        $('#layout-builder-content').delegate('.widget .action-remove', 'click', function(e){
            e.preventDefault();
            e.stopPropagation();
            var parent_entry = $(this).closest('.widget').closest('.entry');
            
            $(this).closest('.widget').remove();
            
            // Trigger child change event for parent
            parent_entry.trigger('child.update');
        });
        
        /*** Design Action ***/
        
        // Duplicate design
        $('#layout-builder-content').delegate('.design .action-duplicate', 'click', function(e){
            e.preventDefault();
            e.stopPropagation();
            var original_design = $(this).closest('.design');
            original_design.after(original_design.clone().data(original_design.data()));
            
            // Trigger child change event for parent
            original_design.closest('.entry').trigger('child.update');
        });
        
        // hide design
        $('#layout-builder-content').delegate('.design .action-hide', 'click', function(e){
            e.preventDefault();
            e.stopPropagation();
            var design = $(this).closest('.design');
            
            if(design.data('device_' + $('#layout-builder-content').data('device') + '_hidden')){
                design.removeClass('gc-hidden-' + $('#layout-builder-content').data('device'));
                design.data('device_' + $('#layout-builder-content').data('device') + '_hidden', 0);
                $(this).html('<i class="fa fa-eye-slash" aria-hidden="true"></i>');
            } else {
                design.addClass('gc-hidden-' + $('#layout-builder-content').data('device'));
                design.data('device_' + $('#layout-builder-content').data('device') + '_hidden', 1);
                $(this).html('<i class="fa fa-eye" aria-hidden="true"></i>');
            }
            
            // Trigger child change event for parent
            design.closest('.entry').trigger('child.update');
        });

        // remove design
        $('#layout-builder-content').delegate('.design .action-remove', 'click', function(e){
            e.preventDefault();
            e.stopPropagation();
            var parent_entry = $(this).closest('.design').closest('.entry');
            
            $(this).closest('.design').remove();
            
            // Trigger child change event for parent
            parent_entry.trigger('child.update');
        });
        
        /*** Content Action ***/
        
        // Duplicate content
        $('#layout-builder-content').delegate('.content .action-duplicate', 'click', function(e){
            e.preventDefault();
            e.stopPropagation();
            var original_content = $(this).closest('.content');
            original_content.after(original_content.clone().data(original_content.data()));
            
            // Trigger child change event for parent
            original_content.closest('.entry').trigger('child.update');
        });
        
        // hide content
        $('#layout-builder-content').delegate('.content .action-hide', 'click', function(e){
            e.preventDefault();
            e.stopPropagation();
            var content = $(this).closest('.content');
            
            if(content.data('device_' + $('#layout-builder-content').data('device') + '_hidden')){
                content.removeClass('gc-hidden-' + $('#layout-builder-content').data('device'));
                content.data('device_' + $('#layout-builder-content').data('device') + '_hidden', 0);
                $(this).html('<i class="fa fa-eye-slash" aria-hidden="true"></i>');
            } else {
                content.addClass('gc-hidden-' + $('#layout-builder-content').data('device'));
                content.data('device_' + $('#layout-builder-content').data('device') + '_hidden', 1);
                $(this).html('<i class="fa fa-eye" aria-hidden="true"></i>');
            }
            
            // Trigger child change event for parent
            content.closest('.entry').trigger('child.update');
        });

        // remove content
        $('#layout-builder-content').delegate('.content .action-remove', 'click', function(e){
            e.preventDefault();
            e.stopPropagation();
            var parent_entry = $(this).closest('.content').closest('.entry');
            
            $(this).closest('.content').remove();
            
            // Trigger child change event for parent
            parent_entry.trigger('child.update');
        });


        // Device switch
        $('.layout-builder-header [data-toggle="device-switch"]').on('click', function(event){
            event.preventDefault();

            // add active class
            $(this).closest(".nav-tabs").children('li').removeClass('active');
            $(this).parent().addClass('active');

            // add device class on target
            var layout_builder_content = $('#layout-builder-content');
            layout_builder_content.removeClass('device-xl device-lg device-md device-sm device-xs');
            layout_builder_content.addClass('device-' + $(this).data('device'));
            layout_builder_content.data('device', $(this).data('device'));
            
            $(this).trigger('device.change');
        });
        
        $('.select-device a[data-toggle="pill"]').on('click', function(){
            $('[data-toggle="device-switch"][data-device="' + $(this).data('device') + '"]').trigger('click');
        });
});

