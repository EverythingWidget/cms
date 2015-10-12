/*global window, document, console, navigator */
/*!
 *  @name       editor
 *  @date       Oct 2013
 *  @by         mjbp
 *  @roadmap    - paste without styles
 - trim leading and trailing whitespace when applying inline styles
 - IE9
 - JSONify content for further integration
 - leverage localStorage to save as going
 */

function log(w) {
   'use strict';
   console.log(w);
}

function MJEditor(selector, opts) {
   'use strict';

   return this.init(selector, opts);
}
(function (w, d) {
   'use strict';

   var toolkit = {
      extend: function (b, a) {
         var p;
         if (b === undefined) {
            return a;
         }
         for (p in a) {
            if (a.hasOwnProperty(p)) {
               b[p] = a[p];
            }
         }
         return b;
      },
      forEach: function (a, fn, scope) {
         var i, l = a.length;
         if ([].forEach) {
            return a.forEach(fn);
         }
         for (i = 0; i < l; i += 1) {
            if (a.hasOwnProperty(i)) {
               fn.call(scope, a[i], i, a);
            }
         }
      },
      on: function (element, event, fn) {
         if (element.addEventListener) {
            element.addEventListener(event, fn, false);
         } else {
            element.attachEvent('on' + event, fn);
         }
      }, /* next functions courteousy of Tim Down, taken from Stack Overflow */
      selection: {
         saveSelection: function (containerEl) {

            var start,
                    range = w.getSelection().getRangeAt(0),
                    preSelectionRange = range.cloneRange();
            preSelectionRange.selectNodeContents(containerEl);
            preSelectionRange.setEnd(range.startContainer, range.startOffset);
            start = preSelectionRange.toString().length;

            return {
               start: start,
               end: start + range.toString().length
            };
         },
         restoreSelection: function (containerEl, savedSel) {
            var i,
                    sel,
                    charIndex = 0,
                    range = d.createRange(),
                    nodeStack = [containerEl],
                    node,
                    nextCharIndex,
                    result,
                    foundStart = false,
                    stop = false;
            range.setStart(containerEl, 0);
            range.collapse(true);

            while (!stop) {
               node = nodeStack.pop();
               if (node !== undefined && node.nodeType === 3) {
                  nextCharIndex = charIndex + node.length;
                  if (!foundStart && savedSel.start >= charIndex && savedSel.start <= nextCharIndex) {
                     range.setStart(node, savedSel.start - charIndex);
                     foundStart = true;
                  }
                  if (foundStart && savedSel.end >= charIndex && savedSel.end <= nextCharIndex) {
                     range.setEnd(node, savedSel.end - charIndex);
                     stop = true;
                  }
                  charIndex = nextCharIndex;
               } else {
                  if (node !== undefined) {
                     i = node.childNodes.length;
                     while (i >= 0) {
                        nodeStack.push(node.childNodes[i]);
                        i -= 1;
                     }
                  }
               }

            }

            sel = w.getSelection();
            sel.removeAllRanges();
            result = sel.addRange(range);
         },
         atEndOfNode: function (range) {
            var restOfNode,
                    postRange = d.createRange();
            postRange.selectNodeContents(range.endContainer);
            postRange.setStart(range.endContainer, range.endOffset);
            restOfNode = postRange.cloneContents().textContent.length;
            return restOfNode === 0 ? true : false;
         }
      },
      isChrome: function () {
         return navigator.userAgent.toLowerCase().indexOf('chrome') > -1;
      }
   };


   MJEditor.prototype = {
      defaults: {
         delay: 0,
         buttons: ['b', 'i', 'blockquote', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'a', 'cancel'],
         nonBreakable: ['a', 'i', 'b', 'p', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'li']
      },
      placeUI: function () {
         this.range = this.selection.getRangeAt(0);
         var limit = 5,
                 limitR = (w.innerWidth - this.gui.clientWidth) - 5,
                 boundary = this.range.getBoundingClientRect(),
                 guiLeft = (((boundary.right - boundary.left) / 2) + boundary.left) - (this.gui.clientWidth / 2);

         guiLeft = guiLeft < limit ? limit : guiLeft > limitR ? limitR : guiLeft;

         var temp = boundary.top - (this.gui.clientHeight + 8) + window.pageYOffset;
         this.gui.style.top = temp + "px";
         if (temp < 0)
         {
            this.gui.style.top = boundary.bottom + 8 + window.pageYOffset + "px";
         }

         this.gui.style.left = guiLeft + "px";
         return this;
      },
      showUI: function () {
         this.placeUI()
                 .gui.className = "active";
         return this;
      },
      hideUI: function (self) {
         this.gui.className = this.gui.className.replace(/active/g, '').replace(/\s{2}/g, ' ');
         this.gui.style.top = "-100px";
         return this;
      },
      resetButtonState: function () {
         var i,
                 buttons = this.gui.querySelectorAll('button'),
                 l = buttons.length;

         for (i = 0; i < l; i += 1) {
            buttons[i].className = buttons[i].className.replace(/active/g, '').replace(/\s{2}/g, ' ');
         }
         return this;
      },
      updateButtonState: function () {
         var i,
                 button,
                 parentNodes = this.findParentNodes(this.selection.anchorNode),
                 l = parentNodes.length;

         this.resetButtonState();

         for (i in parentNodes) {
            if (parentNodes.hasOwnProperty(i)) {
               //console.log(i);
               button = d.getElementById('editor-' + i.toLowerCase());
               if (button && button.className.indexOf('active') === -1) {
                  button.className = button.className + ' active';
               }
            }
         }
         var ta = $(this.selection.anchorNode.parentNode).css('text-align');
         $("button[data-command='" + ta + "']").addClass("active");

         return this;
      },
      bindUI: function ()
      {
         var buttons = this.gui.querySelectorAll('button'),
                 i,
                 self = this,
                 buttonTrigger = function (e) {
                    var command = this.getAttribute('data-command');
                    e.preventDefault();
                    e.stopPropagation();
                    self.executeCommand(command);
                 };
         for (i = 0; i < buttons.length; i += 1) {
            toolkit.on(buttons[i], 'click', buttonTrigger);
         }
         return this;
      },
      cleanUp: function (styleType) {
         var i, l, j, k,
                 self = this,
                 child,
                 disallowedEls = ['BR', 'SPAN'],
                 disallowedAttrs = [],
                 children,
                 elsToFix = {remove: [], swap: []};

         if (this.liveElement !== undefined) {
            children = this.liveElement.getElementsByTagName('*');
            l = children.length;

            for (i = 0; i < l; i += 1) {
               child = children[i];
               child.normalize();

               //remove unwanted attributes
               for (j = 0; j < disallowedAttrs.length; j += 1) {
                  if (child.hasAttribute(disallowedAttrs[j])) {
                     child.removeAttribute(disallowedAttrs[j]);
                  }
               }
               //check if empty/whitespace-only and flag as unwanted
               if (/^\s*$/.test(child.textContent) && child.nodeName !== 'HR') {
                  //elsToFix.remove.push(child);
               } else {
                  //flag unwanted nodes
                  for (k = 0; k < disallowedEls.length; k += 1) {
                     if (disallowedEls[k] === child.tagName) {
                        elsToFix.remove.push(child);
                     }
                  }
                  //check for orphaned LIs
                  if (child.nodeName === 'LI' && (child.parentNode.nodeName !== 'UL' && child.parentNode.nodeName !== 'OL')) {
                     elsToFix.swap.push(child);
                  }
               }
            }
            //remove unwanted
            if (elsToFix.remove.length) {
               for (i = 0; i < elsToFix.remove.length; i += 1) {
                  self.removeNode(elsToFix.remove[i]);
               }
            }
            if (elsToFix.swap.length) {
               for (i = 0; i < elsToFix.swap.length; i += 1) {
                  self.swapNode(elsToFix.swap[i]);
               }
            }
         }
         return self;
      },
      isBlockStyle: function (style) {
         return style !== 'bold' || style !== 'italic';
      },
      executeCommand: function (c)
      {
         var self = this, display;
         var incompatibleElements = {
            'UL': ['BLOCKQUOTE', 'P', 'H1', 'H2', 'H3', 'H4', 'H5', 'H6'],
            'OL': ['BLOCKQUOTE', 'P', 'H1', 'H2', 'H3', 'H4', 'H5', 'H6'],
            'BLOCKQUOTE': ['LI', 'OL', 'UL'],
            'heading': ['BLOCKQUOTE', 'LI', 'OL', 'UL']
         };
         var removeIncompatibles = function (p, a)
         {
            var i, l = a.length;

            for (i = 0; i < l; i += 1) {
               if (!!p[a[i]]) {
                  self.removeNode(p[a[i]]);
               }
            }
            return;
         };
         var styleActions = {
            'bold': function () {
               d.execCommand('bold', false);
            },
            'italic': function () {
               d.execCommand('italic', false);
            },
            'ul': function () {
               styleActions.list('UL');
            },
            'ol': function () {
               styleActions.list('OL');
            },
            'quote': function () {
               var parentNodes = self.findParentNodes(self.selection.anchorNode),
                       text,
                       incompatibles = incompatibleElements.heading;

               removeIncompatibles(parentNodes, incompatibles);

               if (!!parentNodes.BLOCKQUOTE) {
                  d.execCommand('formatBlock', false, 'p');
                  d.execCommand('outdent');
               } else {
                  d.execCommand('formatBlock', false, 'blockquote');
               }
            },
            'left': function () {
               d.execCommand('justifyleft', false);
            },
            'center': function () {
               d.execCommand('justifycenter', false);
            },
            'right': function () {
               d.execCommand('justifyright', false);
            },
            'justify': function () {
               d.execCommand('justifyFull', false);
            },
            'h1': function () {
               styleActions.heading('H1');
            },
            'h2': function () {
               styleActions.heading('H2');
            },
            'h3': function () {
               styleActions.heading('H3');
            },
            'h4': function () {
               styleActions.heading('H4');
            },
            'h5': function () {
               styleActions.heading('H5');
            },
            'h6': function () {
               styleActions.heading('H6');
            },
            'heading': function (h) {
               var parentNodes = self.findParentNodes(self.selection.anchorNode),
                       incompatibles = incompatibleElements.heading;

               removeIncompatibles(parentNodes, incompatibles);

               if (!!parentNodes[h]) {
                  d.execCommand('formatBlock', false, 'p');
                  d.execCommand('outdent');
               } else {
                  d.execCommand('formatBlock', false, h);
               }
            },
            'list': function (listType)
            {
               var currentNode = self.selection.anchorNode.nodeType == 3 ? self.selection.anchorNode.parentNode : self.selection.anchorNode;
               var parentNodes = self.findParentNodes(currentNode),
                       incompatibles = incompatibleElements[listType],
                       execList = function (cmd)
                       {
                          var range = w.getSelection().getRangeAt(0),
                                  dummy,
                                  newRange,
                                  ceNode;
                          try
                          {
                             document.execCommand(cmd, false, null);
                             range = w.getSelection().getRangeAt(0);
                          }
                          catch (e)
                          {
                             //special case for Mozilla Bug #442186
                             if (e && e.result === 2147500037) {
                                //probably firefox bug 442186 - workaround
                                dummy = document.createElement('div');

                                //find node with contentEditable
                                ceNode = range.startContainer.parentNode;
                                while (ceNode && ceNode.contentEditable !== 'true') {
                                   ceNode = ceNode.parentNode;
                                }
                                if (!ceNode) {
                                   throw 'Selected node is not editable!';
                                }
                                ceNode.insertBefore(dummy, ceNode.childNodes[0]);
                                d.execCommand(cmd, false, null);
                                dummy.parentNode.removeChild(dummy);
                             }
                          }
                          newRange = d.createRange();
                          //console.log(range);
                          newRange.selectNodeContents(range.startContainer);
                          newRange.collapse(false);
                          //self.selection = w.getSelection();
                          self.selection.removeAllRanges();
                          self.selection.addRange(newRange);
                          self.savedSelection = toolkit.selection.saveSelection(self.liveElement);
                       };

               if (!!parentNodes[listType])
               {
                  d.execCommand('formatBlock', false, 'p');
                  d.execCommand('outdent');
               }
               else
               {
                  if (toolkit.isChrome()) {
                     removeIncompatibles(parentNodes, incompatibles);
                  }
                  if (listType === 'UL') {
                     execList('insertunorderedlist');
                  } else {
                     execList('insertorderedlist');
                  }
               }
            },
            'a': function () {
               var parentNodes = self.findParentNodes(self.selection.anchorNode);
               self.linkMode = true;

               if (parentNodes.A)
               {
                  d.getElementById('editor-link-field').value = parentNodes.A.href;
               }
               //self.cancelLink();
               //} else {
               self.gui.className = self.gui.className + " link-mode";
               w.setTimeout(function () {
                  d.getElementById('editor-link-field').focus();
               }, 100);
               //self.addLink();
               //}
            },
         };
         var actions =
                 {
                    'cancel': function ()
                    {
                       self.cancelLink();
                    },
                    'back': function ()
                    {
                       self.exitLinkMode();
                    },
                    'enter': function ()
                    {
                       self.addHref();
                    }
                 }
         if (actions[c])
         {
            actions[c]();
            return;
         }
         if (!self.liveElement)
            return;
         self.savedSelection = toolkit.selection.saveSelection(self.liveElement);
         styleActions[c]();
         if (self.isBlockStyle(c))
         {
            self.cleanUp();
         }
         //toolkit.selection.restoreSelection(self.liveElement, self.savedSelection);

         self.updateButtonState().placeUI();

         return this;
      },
      addLink: function () {
         d.execCommand('unlink', false);
         d.execCommand('createLink', false, '/');
      },
      addHref: function () {
         var self = this,
                 linkField = d.getElementById('editor-link-field'),
                 url = linkField.value;
         toolkit.selection.restoreSelection(self.liveElement, self.savedSelection);

         d.execCommand('unlink', false);

         if (url.trim() !== "") {
            if (!url.match("^(http|https)://")) {
               url = "http://" + url;
            }
            d.execCommand('createLink', false, url);
         }
         self.exitLinkMode();
      },
      cancelLink: function () {
         var self = this,
                 parentNodes;
         toolkit.selection.restoreSelection(self.liveElement, self.savedSelection);
         self.selection = w.getSelection();

         document.execCommand('unlink', false);

         self.exitLinkMode();
      },
      exitLinkMode: function () {
         var self = this;

         d.getElementById('editor-link-field').value = '';
         d.getElementById('editor-link-field').blur();

         self.linkMode = false;

         toolkit.selection.restoreSelection(self.liveElement, self.savedSelection);

         self.selection.getRangeAt(0).collapse(false);
         this.gui.className = self.gui.className.replace(/link-mode/g, '').replace(/\s{2}/g, ' ');
         self.updateButtonState();
         //self.hideUI();
      },
      removeNode: function (node) {
         var self = this,
                 replacedChild,
                 fragment = d.createDocumentFragment();

         while (node.firstChild) {
            fragment.appendChild(node.firstChild);
         }
         replacedChild = node.parentNode.replaceChild(fragment, node);

         return replacedChild;
      },
      swapNode: function (node, type) {
         var fragment = d.createDocumentFragment(),
                 replacement;
         type = type || 'p';
         replacement = d.createElement(type);
         while (node.firstChild) {
            fragment.appendChild(node.firstChild);
         }
         replacement.appendChild(fragment);
         node.parentNode.replaceChild(replacement, node);

         return;
      },
      findParentNodes: function (element) {
         var nodeNames = {};
         while (element.parentNode) {
            nodeNames[element.nodeName] = element;
            element = element.parentNode;
         }
         return nodeNames;
      },
      isList: function (node) {
         //var parentNodes = this.findParentNodes(this.selection.focusNode);
         var parentNodes = this.findParentNodes(node);
         return parentNodes.LI;
      },
      isHeading: function (el) {
         var headings = ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'];
         return headings.indexOf(el.toLowerCase()) !== -1 ? true : false;
      },
      isListItem: function (el) {
         var list = ['li', 'ul', 'ol'];
         return list[el.toLowerCase()] ? true : false;
      },
      enterHandler: function (e)
      {
         var range, parentNode, postRange, rangeParent, previousNode, previousElement, currentNode, nextNode, nextElement, newRange, newEl, sel,
                 self = this;

         range = self.selection.getRangeAt(0);
         parentNode = range.startContainer.parentNode;
         currentNode = range.startContainer;
         //console.log(this.selection.anchorNode.nodeType == 3 ? this.selection.anchorNode.parentNode : this.selection.anchorNode);
         currentNode = this.selection.anchorNode.nodeType == 3 ? this.selection.anchorNode.parentNode : this.selection.anchorNode;
         //console.log(parentNode)
//         console.log(currentNode)
         //console.log(self.isList(parentNode));
         var listItem = self.isList(currentNode);
         if (!listItem)
         {
            if (self.isHeading(parentNode.nodeName) || parentNode.nodeName === 'BLOCKQUOTE')
            {
               e.preventDefault();
               self.newParagraph(null, parentNode.parentNode, parentNode.nextSibling);
            }
            else
            {
               if (!self.isList(currentNode))
               {
                  // Enter on a empty p
                  /*if (currentNode.textContent.trim() === '')
                   {
                   e.preventDefault();
                   self.cleanUp();
                   self.newParagraph(parentNode.parentNode);
                   console.log('aaaaaaaaa');
                   }
                   else
                   {*/

                  if (/^1\.\s/.test(currentNode.textContent))
                  {
                     e.preventDefault();
                     //console.log(currentNode.textContent.replace(/^1\.\s/, 'xxxxx'));
                     currentNode.textContent = currentNode.textContent.replace(/^1\.\s/, '');
                     self.executeCommand('ol');
                  }
                  else
                  {
                     if (/^-\s/.test(currentNode.textContent))
                     {
                        //alert()
                        
                        e.preventDefault();
                                  
                        //currentNode.parentNode.removeChild(currentNode);
                        self.executeCommand('ul');
                        console.log(currentNode)
                        currentNode.textContent = currentNode.textContent.replace(/^-\s/, '');              
                     }
                     else
                     {
                        console.log('normal para');
                        e.preventDefault();
                        self.cleanUp();
                        self.newParagraph(currentNode);
                     }
                  }
               }
               //}
            }
         }
         else if (listItem.firstChild)
         {
            //e.preventDefault();
            console.log(currentNode.firstChild);
            if (currentNode.firstChild.nodeName.toLowerCase() === "br")
            {
               e.preventDefault();
               self.newParagraph(currentNode);
               currentNode.parentNode.removeChild(currentNode);
               //self.cleanUp();
            }

            /*var li = d.createElement('li');
             currentNode.parentNode.insertBefore(li, currentNode.nextSibling);
             newRange = d.createRange();
             //console.log(range);
             //newRange.selectNodeContents(li);
             newRange.selectNode(li);
             newRange.collapse();
             //self.selection = w.getSelection();
             //self.selection.removeAllRanges();
             self.selection.addRange(newRange);*/
            //if(!currentNode.parentNode)

         }
         else
         {
            e.preventDefault();
            self.cleanUp();
            self.newParagraph();
            console.log('mjbp: New paragraph on enter');
            //alert();
         }
         return self;
      },
      backspaceHandler: function (e) {
         var self = this;
         setTimeout(function () {
            var currentNode = self.selection.anchorNode.nodeType == 3 ? self.selection.anchorNode.parentNode : self.selection.anchorNode;
            if ($(currentNode).is(".row") && currentNode.childNodes.length <= 1)
            {
               $(currentNode).remove();
            }
         }, 0);

         /*var range = w.getSelection().getRangeAt(0);
          //console.log($(range));
          console.log(e);
          //if(range.nodeName)
          if (!!toolkit.isChrome()) {
          this.cleanUp();
          }*/
      },
      newParagraph: function (target, parent, place)
      {
         //console.log(this.activeComponent);
         //console.log("NEW P");
         var self = this;
         var currentNode, range, paragraph, newRange, liveP;
         target = target || this.activeComponent[0];
         this.selection = w.getSelection();
         range = this.selection.getRangeAt(0);
         var disallowedParents = ['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p', 'ul', 'ol'];
         //currentNode = range.startContainer;
         //currentNode = (this.selection.anchorNode.nodeType == 3 ? this.selection.anchorNode.parentNode : this.selection.anchorNode);

         paragraph = d.createElement('p');
         liveP = d.getElementById('editor-new-p');

         if (liveP)
         {
            //liveP.removeAttribute('id');
            liveP.id = "";
         }
         paragraph.id = 'editor-new-p';
         paragraph.innerHTML = '&nbsp;';
         //console.log(target)
         if (target)
         {
            //this.activeComponent[0].innerHTML = this.activeComponent[0].innerHTML.replace('&nbsp;','');
            //console.log(this.activeComponent[0].innerHTML)
            // if activeComponent is a disallowed parent then add the new p next to it
            //console.log(disallowedParents[this.activeComponent[0].nodeName.toLowerCase()])
            if (disallowedParents.indexOf(target.nodeName.toLowerCase()) !== -1)
            {

               target.parentNode.insertBefore(paragraph, target.nextSibling);
            }
            // else append the p to the activeComponent
            else
               target.appendChild(paragraph);
         }
         /*if (parent)
          {
          parent.innerHTML = parent.innerHTML.replace('&nbsp;','');
          parent.insertBefore(newEl, place);
          }
          else if (target) 
          {
          target.appendChild(newEl);
          //this.liveElement.appendChild(newEl);
          }*/
         /*else 
          {
          this.liveElement.insertBefore(newEl, target);
          }*/

         //newRange.selectNodeContents(newEl);
         //console.log('para');
         //console.log(parent);
         newRange = d.createRange();
         //newRange.setStart(paragraph, 0);
         //newRange.setEnd(paragraph, 0);  
         // set new added paragraph as the activeComponent
         this.activeComponent = $(paragraph);
         newRange.selectNode(paragraph.childNodes[0]);
         //newRange.collapse();
         self.selection = w.getSelection();
         self.selection.removeAllRanges();
         self.selection.addRange(newRange);

         //document.execCommand('delete', false, null);
      },
      initListeners: function () {
         var self = this,
                 cancelBtn,
                 i,
                 l = this.elements.length,
                 placeHolder = function (el) {
                    var placeHolderText = el.getAttribute('data-placeholder');
                    //self.cleanUp();
                    if (el.innerHTML.trim() === '' && el.className.indexOf('editor-placeholder') === -1) {
                       el.className += ' editor-placeholder';
                    } else {
                       el.className = el.className.replace(/editor-placeholder/g, '').replace(/\s{2}/g, ' ');
                    }
                 },
                 focus = function (e) {
                    var el = e.target || e;
                    if (el.childNodes.length === 0)
                    {
                       placeHolder(el);
                    }
                    //self.showUI();
                 },
                 blur = function (e)
                 {
                    var el = e.target || e;
                    self.cleanUp();
                    placeHolder(el);
                    if (self.selection && self.selection.isCollapsed)
                       self.hideUI();
                 },
                 highlightListener = function (e) {
                    var me = this;
                    var sel = d.getSelection();
                    var currentNode = (sel.anchorNode.nodeType == 3 ? sel.anchorNode.parentNode : sel.anchorNode);
                    //console.log(currentNode)
                    w.setTimeout(function () {
                       self.selection = w.getSelection();
                       self.liveElement = me;
//console.log(self.selection);
                       if (self.liveElement.className.indexOf('editor-heading') === -1)
                       {
                          if (self.selection.isCollapsed === false)
                          {
                             //show editor
                             self.updateButtonState();
                             self.showUI();
                          }
                          else
                          {
                             self.hideUI();
                          }
                       }
                    }, 1);
                 },
                 keyDownListener = function (e) {
                    var sel = d.getSelection();

                    var range;

                    if (!sel.anchorNode)
                       return;
                    range = sel.getRangeAt(0);

                    self.currentNode = (sel.anchorNode.nodeType == 3 ? sel.anchorNode.parentNode : sel.anchorNode);
                    //console.log(self.currentNode);
                    if (e.keyCode === 13) {
                       self.enterHandler(e);
                    }
                    else
                    {
                       if (e.keyCode === 8) {
                          self.backspaceHandler(e);
                       } else if (e.keyCode === 46) {
                          // delete
                       } else {
                          if (self.defaults.nonBreakable.indexOf(self.currentNode.nodeName.toLowerCase()) === -1 && !self.isHeading(self.currentNode.nodeName))
                          {
                             self.newParagraph(self.currentNode);
                          }
                          else if (self.isListItem(self.currentNode.parentNode.tagName) && self.currentNode.parentNode && !self.isHeading(self.currentNode.parentNode.nodeName) && self.currentNode.parentNode.tagName !== 'P' && self.currentNode.tagName !== 'P')
                          {
                             //console.log($(self.currentNode));
                             //console.log('inja' + self.currentNode.parentNode.tagName);
                             self.newParagraph(null, self.currentNode);
                          }
                          else
                          {

                          }
                       }
                    }
                    highlightListener.call(this);
                 },
                 keyUpListener = function (e) {
                    highlightListener.call(this);
                 },
                 linkInputListener = function (e) {
                    if (e.keyCode === 13) {
                       e.preventDefault();
                       self.addHref();
                    }
                 };

         for (i = 0; i < l; i += 1) {
            //alert(this.elements[i].outerHTML);
            placeHolder(this.elements[i]);
            toolkit.on(this.elements[i], 'mouseup', highlightListener);
            toolkit.on(this.elements[i], 'keydown', keyDownListener);
            toolkit.on(this.elements[i], 'keyup', keyUpListener);
            toolkit.on(this.elements[i], 'blur', blur);
            toolkit.on(this.elements[i], 'focus', focus);
            this.liveElement = this.elements[i];
         }

         toolkit.on(d.getElementById('editor-link-field'), 'keydown', linkInputListener);

         return this;
      },
      initEditableElements: function (selector) {
         var i,
                 l = this.elements.length,
                 headerAttribute = toolkit.isChrome() ? 'plaintext-only' : true;
         for (i = 0; i < l; i += 1) {
            if (this.elements[i].className.indexOf('editor-heading') > -1) {
               //this.elements[i].setAttribute('contentEditable', headerAttribute);
            } else {
               //this.elements[i].setAttribute('contentEditable', true);
            }
         }
         return this;
      },
      initButtons: function () {
         var self = this,
                 buttons = [].slice.call(this.gui.getElementsByTagName('button'));

         /*toolkit.forEach(buttons, function (key) {
          if (self.defaults.buttons.indexOf(key.id.replace(/editor-/, '')) === -1) {
          key.style.display = 'none';
          }
          }, self);*/

         return this;
      },
      init: function (selector, opts) {
         if (!selector)
            return this;
         this.defaults = toolkit.extend(this.defaults, opts);
         //alert(typeof(selector));
         if (typeof (selector) === 'object')
            this.elements = [selector];
         else if (selector)
            this.elements = d.querySelectorAll(selector);

         if (this.elements.length === 0) {
            return;
         }

         this.gui = d.getElementById('editor');
         this.linkMode = false;

         return this.initEditableElements(selector)
                 .initButtons()
                 .initListeners()
                 .bindUI();
      },
      destroy: function ()
      {
         if (this.elements)
         {
            var i, l = this.elements.length;
            for (i = 0; i < l; i += 1)
            {
               var old_element = this.elements[i];
               var new_element = old_element.cloneNode(true);
               //console.log(old_element.parentNode);
               if (old_element.parentNode)
                  old_element.parentNode.replaceChild(new_element, old_element);
            }
         }
      },
      refresh: function (selector, opts) {
         this.destroy();
         this.init(selector, opts);
      }

   };

}(window, document));