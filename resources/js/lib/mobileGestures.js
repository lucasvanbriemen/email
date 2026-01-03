/**
 * Mobile Gestures Handler
 * Manages pull-to-refresh and swipe-to-close gestures for mobile devices
 */

export const mobileGestures = {
  setupPullToRefresh(container, onRefresh) {
    if (!IS_MOBILE) return;

    let startY = 0;
    let currentY = 0;
    let isPulling = false;
    const REFRESH_THRESHOLD = 100;
    const indicator = document.createElement('div');
    indicator.className = 'pull-to-refresh-indicator';
    container.parentElement?.insertBefore(indicator, container);

    const handleTouchStart = (e) => {
      const scrollTop = container.scrollTop;
      if (scrollTop === 0) {
        startY = e.touches[0].clientY;
        isPulling = true;
      }
    };

    const handleTouchMove = (e) => {
      if (!isPulling) return;

      currentY = e.touches[0].clientY;
      const diff = currentY - startY;

      if (diff > 0) {
        e.preventDefault();
        indicator.style.transform = `translateY(${diff}px)`;
        indicator.style.opacity = Math.min(diff / REFRESH_THRESHOLD, 1);
        indicator.innerHTML = diff > REFRESH_THRESHOLD
          ? '↑ Release to refresh'
          : '↓ Pull to refresh';
      }
    };

    const handleTouchEnd = () => {
      const diff = currentY - startY;
      isPulling = false;

      if (diff > REFRESH_THRESHOLD) {
        indicator.innerHTML = 'Refreshing...';
        indicator.classList.add('refreshing');
        onRefresh?.();
      }

      setTimeout(() => {
        indicator.style.transform = 'translateY(0)';
        indicator.style.opacity = '0';
        indicator.classList.remove('refreshing');
      }, 300);

      startY = 0;
      currentY = 0;
    };

    container.addEventListener('touchstart', handleTouchStart, false);
    container.addEventListener('touchmove', handleTouchMove, false);
    container.addEventListener('touchend', handleTouchEnd, false);

    return () => {
      container.removeEventListener('touchstart', handleTouchStart);
      container.removeEventListener('touchmove', handleTouchMove);
      container.removeEventListener('touchend', handleTouchEnd);
      indicator.remove();
    };
  },

  setupSwipeToClose(element, onClose) {
    if (!IS_MOBILE) return;

    let startX = 0;
    let currentX = 0;
    let startY = 0;
    let isGesturing = false;
    const SWIPE_THRESHOLD = 50;
    const MAX_SWIPE_DISTANCE = 80;

    const handleTouchStart = (e) => {
      startX = e.touches[0].clientX;
      startY = e.touches[0].clientY;
      isGesturing = true;
    };

    const handleTouchMove = (e) => {
      if (!isGesturing) return;

      currentX = e.touches[0].clientX;
      const currentY = e.touches[0].clientY;
      const diffX = currentX - startX;
      const diffY = Math.abs(currentY - startY);

      // Only trigger horizontal swipe if movement is more horizontal than vertical
      if (Math.abs(diffX) > diffY && diffX > 0 && diffX < MAX_SWIPE_DISTANCE) {
        e.preventDefault();
        element.style.transform = `translateX(${diffX}px)`;
        element.style.opacity = 1 - diffX / MAX_SWIPE_DISTANCE * 0.3;
      }
    };

    const handleTouchEnd = () => {
      const diff = currentX - startX;
      isGesturing = false;

      if (diff > SWIPE_THRESHOLD) {
        // Swipe right to close
        element.style.transition = 'all 0.3s ease-out';
        element.style.transform = 'translateX(100%)';
        element.style.opacity = '0';
        onClose?.();

        setTimeout(() => {
          element.style.transition = 'none';
          element.style.transform = 'translateX(0)';
          element.style.opacity = '1';
        }, 500);
      } else {
        // Reset position
        element.style.transition = 'all 0.2s ease-out';
        element.style.transform = 'translateX(0)';
        element.style.opacity = '1';
      }

      startX = 0;
      currentX = 0;
      startY = 0;
    };

    element.addEventListener('touchstart', handleTouchStart, false);
    element.addEventListener('touchmove', handleTouchMove, false);
    element.addEventListener('touchend', handleTouchEnd, false);

    return () => {
      element.removeEventListener('touchstart', handleTouchStart);
      element.removeEventListener('touchmove', handleTouchMove);
      element.removeEventListener('touchend', handleTouchEnd);
    };
  }
};

export default mobileGestures;
