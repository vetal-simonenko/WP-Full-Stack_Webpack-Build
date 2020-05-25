export default (() => {
  const ElementPrototype = window.Element.prototype
  if (typeof ElementPrototype.matches !== 'function') {
    ElementPrototype.matches = ElementPrototype.msMatchesSelector || ElementPrototype.mozMatchesSelector || ElementPrototype.webkitMatchesSelector || function matches (selector) {
      const element = this
      const elements = (element.document || element.ownerDocument).querySelectorAll(selector)
      let index = 0

      while (elements[index] && elements[index] !== element) {
        ++index
      }

      return Boolean(elements[index])
    }
  }

  if (typeof ElementPrototype.closest !== 'function') {
    ElementPrototype.closest = function closest (selector) {
      let element = this

      while (element && element.nodeType === 1) {
        if (element.matches(selector)) {
          return element
        }

        element = element.parentNode
      }

      return null
    }
  }

  ((arr) => {
    arr.forEach(function (item) {
      if (item.hasOwnProperty('append')) {
        return
      }
      Object.defineProperty(item, 'append', {
        configurable: true,
        enumerable: true,
        writable: true,
        value: function append () {
          const argArr = Array.prototype.slice.call(arguments)
          const docFrag = document.createDocumentFragment()

          argArr.forEach(function (argItem) {
            const isNode = argItem instanceof Node
            docFrag.appendChild(isNode ? argItem : document.createTextNode(String(argItem)))
          })

          this.appendChild(docFrag)
        }
      })
    })
  })([Element.prototype, Document.prototype, DocumentFragment.prototype]);

  (function () {
    var arr = [window.Element, window.CharacterData, window.DocumentType]
    var args = []

    arr.forEach(function (item) {
      if (item) {
        args.push(item.prototype)
      }
    });

    (function (arr) {
      arr.forEach(function (item) {
        if (item.hasOwnProperty('remove')) {
          return
        }
        Object.defineProperty(item, 'remove', {
          configurable: true,
          enumerable: true,
          writable: true,
          value: function remove () {
            this.parentNode.removeChild(this)
          }
        })
      })
    })(args)
  })();

  (function (arr) {
    arr.forEach(function (item) {
      if (item.hasOwnProperty('prepend')) {
        return
      }
      Object.defineProperty(item, 'prepend', {
        configurable: true,
        enumerable: true,
        writable: true,
        value: function prepend () {
          var argArr = Array.prototype.slice.call(arguments)
          var docFrag = document.createDocumentFragment()

          argArr.forEach(function (argItem) {
            var isNode = argItem instanceof Node
            docFrag.appendChild(isNode ? argItem : document.createTextNode(String(argItem)))
          })

          this.insertBefore(docFrag, this.firstChild)
        }
      })
    })
  })([Element.prototype, Document.prototype, DocumentFragment.prototype])
})()
