import './../styles/style.scss'
import '@babel/polyfill'
import './utils/polyfills'
import './utils/detectTouch'
import './utils/responsiveHelper'
import ResizeHandler from './utils/resize-active'

import 'intersection-observer' // Polyfill IntersectionObserver API for IE and old Safari
import LazyLoad from 'vanilla-lazyload' // Lazy load images plugin

const resizeHandler = new ResizeHandler()

/** Load Events */
window.addEventListener('DOMContentLoaded', () => {
  /**
   * Init lazyload and polyfill
   */
  const lazyLoadInsance = new LazyLoad()
  lazyLoadInsance.update()
  resizeHandler.init()
})
