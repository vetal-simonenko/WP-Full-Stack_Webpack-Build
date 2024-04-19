export default class ResizeActive {
  constructor () {
    this.resizeClass = 'resize-active'
    this.targetActive = document.getElementsByTagName('html')[0]
    this.timer = false
    this.time = 500
  }

  init () {
    const self = this

    this.listener = function () {
      self.resizeHandler()
    }

    window.addEventListener('resize', this.listener)
    window.addEventListener('orientationchange', this.listener)
  }

  resizeHandler () {
    this.addClassHandler()
    this.clearTimer()
    this.timer = setTimeout(this.removeClassHandler.bind(this), this.time)
  }

  clearTimer () {
    clearTimeout(this.timer)
  }

  removeClassHandler () {
    this.flag = false
    this.targetActive.classList.remove(this.resizeClass)
  }

  addClassHandler () {
    if (!this.flag) {
      this.flag = true
      this.targetActive.classList.add(this.resizeClass)
    }
  }

  destroy () {
    window.removeEventListener('resize', this.listener)
    window.removeEventListener('orientationchange', this.listener)
    this.clearTimer()
    this.removeClassHandler()
  }
}
