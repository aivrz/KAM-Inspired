// 主JavaScript文件 - KAM Inspired Theme (朋友圈版本)
document.addEventListener('DOMContentLoaded', function() {
    
    // ===== 导航菜单功能 =====
    const navToggle = document.querySelector('.nav-toggle');
    const navMenu = document.querySelector('.nav-menu');
    
    if (navToggle && navMenu) {
        navToggle.addEventListener('click', function() {
            navMenu.classList.toggle('active');
            this.classList.toggle('active');
        });
        
        // 点击页面其他区域关闭菜单
        document.addEventListener('click', function(e) {
            if (!navToggle.contains(e.target) && !navMenu.contains(e.target)) {
                navMenu.classList.remove('active');
                navToggle.classList.remove('active');
            }
        });
    }
    
    // ===== 图片预览功能 =====
    const imageModal = document.getElementById('image-modal');
    const modalImage = imageModal.querySelector('.modal-image');
    const modalClose = imageModal.querySelector('.modal-close');
    
    // 点击图片预览
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('moment-image') || e.target.closest('.moment-image')) {
            const img = e.target.tagName === 'IMG' ? e.target : e.target.querySelector('img');
            if (img) {
                modalImage.src = img.src;
                imageModal.classList.add('active');
                document.body.style.overflow = 'hidden';
            }
        }
    });
    
    // 关闭预览
    modalClose.addEventListener('click', closeImageModal);
    imageModal.addEventListener('click', function(e) {
        if (e.target === imageModal) {
            closeImageModal();
        }
    });
    
    function closeImageModal() {
        imageModal.classList.remove('active');
        document.body.style.overflow = '';
    }
    
    // ===== 点赞功能 =====
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('moment-like')) {
            const button = e.target;
            const momentId = button.getAttribute('data-moment-id');
            const isLiked = button.getAttribute('data-liked') === 'true';
            
            likeMoment(momentId, button, isLiked);
        }
    });
    
    function likeMoment(momentId, button, isLiked) {
        // 检查用户是否登录
        if (!isUserLoggedIn()) {
            alert('请先登录后再点赞');
            return;
        }
        
        const data = new FormData();
        data.append('action', 'moment_like');
        data.append('moment_id', momentId);
        
        // 显示加载状态
        const originalText = button.textContent;
        button.textContent = '...';
        button.disabled = true;
        
        fetch(ajaxurl, {
            method: 'POST',
            body: data
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                button.setAttribute('data-liked', result.data.liked);
                button.textContent = result.data.liked ? '👍 已赞' : '👍 赞';
                
                if (result.data.liked) {
                    button.classList.add('active');
                } else {
                    button.classList.remove('active');
                }
                
                // 更新点赞统计
                updateLikeStats(momentId, result.data.like_count);
            } else {
                alert(result.data || '操作失败');
            }
        })
        .catch(error => {
            console.error('点赞错误:', error);
            alert('网络错误，请重试');
        })
        .finally(() => {
            button.disabled = false;
            if (!result || !result.success) {
                button.textContent = originalText;
            }
        });
    }
    
    function updateLikeStats(momentId, likeCount) {
        const momentItem = document.querySelector(`[data-moment-id="${momentId}"]`);
        const stats = momentItem.querySelector('.moment-stats');
        
        let likeSpan = stats.querySelector('.like-count');
        if (likeCount > 0) {
            if (!likeSpan) {
                likeSpan = document.createElement('span');
                likeSpan.className = 'like-count';
                stats.insertBefore(likeSpan, stats.firstChild);
            }
            likeSpan.textContent = likeCount + ' 赞';
            if (likeCount > 1) {
                likeSpan.textContent += ' ';
            }
        } else if (likeSpan) {
            likeSpan.remove();
        }
        
        // 更新点赞列表
        updateLikesList(momentId, likeCount);
    }
    
    function updateLikesList(momentId, likeCount) {
        const momentItem = document.querySelector(`[data-moment-id="${momentId}"]`);
        let likesContainer = momentItem.querySelector('.moment-likes');
        
        if (likeCount > 0) {
            if (!likesContainer) {
                likesContainer = document.createElement('div');
                likesContainer.className = 'moment-likes';
                momentItem.querySelector('.moment-actions').insertAdjacentElement('afterend', likesContainer);
            }
            // 这里可以添加获取点赞用户列表的逻辑
            likesContainer.innerHTML = '👍 您和其他' + (likeCount - 1) + '人';
        } else if (likesContainer) {
            likesContainer.remove();
        }
    }
    
    // ===== 评论功能 =====
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('moment-comment')) {
            const button = e.target;
            const momentId = button.getAttribute('data-moment-id');
            const momentItem = button.closest('.moment-item');
            const commentInput = momentItem.querySelector('.moment-comment-input');
            
            // 切换评论输入框显示
            commentInput.style.display = commentInput.style.display === 'none' ? 'flex' : 'none';
            
            if (commentInput.style.display !== 'none') {
                commentInput.querySelector('.comment-text').focus();
            }
        }
        
        if (e.target.classList.contains('comment-submit')) {
            const button = e.target;
            const momentItem = button.closest('.moment-item');
            const momentId = momentItem.getAttribute('data-moment-id');
            const input = momentItem.querySelector('.comment-text');
            const content = input.value.trim();
            
            if (content) {
                submitComment(momentId, content, momentItem, button);
            }
        }
    });
    
    function submitComment(momentId, content, momentItem, button) {
        // 检查用户是否登录
        if (!isUserLoggedIn()) {
            alert('请先登录后再评论');
            return;
        }
        
        const data = new FormData();
        data.append('action', 'moment_comment');
        data.append('moment_id', momentId);
        data.append('content', content);
        
        // 显示加载状态
        const originalText = button.textContent;
        button.textContent = '发送中...';
        button.disabled = true;
        
        fetch(ajaxurl, {
            method: 'POST',
            body: data
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                addCommentToDOM(result.data.comment, momentItem);
                updateCommentStats(momentId, result.data.comment_count);
                
                // 清空输入框并隐藏
                const input = momentItem.querySelector('.comment-text');
                input.value = '';
                momentItem.querySelector('.moment-comment-input').style.display = 'none';
            } else {
                alert(result.data || '评论失败');
            }
        })
        .catch(error => {
            console.error('评论错误:', error);
            alert('网络错误，请重试');
        })
        .finally(() => {
            button.textContent = originalText;
            button.disabled = false;
        });
    }
    
    function addCommentToDOM(comment, momentItem) {
        let commentsContainer = momentItem.querySelector('.moment-comments');
        if (!commentsContainer) {
            commentsContainer = document.createElement('div');
            commentsContainer.className = 'moment-comments';
            const actions = momentItem.querySelector('.moment-actions');
            actions.insertAdjacentElement('afterend', commentsContainer);
        }
        
        const commentItem = document.createElement('div');
        commentItem.className = 'moment-comment-item';
        commentItem.innerHTML = `
            <span class="moment-comment-author">${comment.author}：</span>
            <span class="moment-comment-text">${comment.content}</span>
        `;
        
        commentsContainer.appendChild(commentItem);
    }
    
    function updateCommentStats(momentId, commentCount) {
        const momentItem = document.querySelector(`[data-moment-id="${momentId}"]`);
        const stats = momentItem.querySelector('.moment-stats');
        
        let commentSpan = stats.querySelector('.comment-count');
        if (commentCount > 0) {
            if (!commentSpan) {
                commentSpan = document.createElement('span');
                commentSpan.className = 'comment-count';
                stats.appendChild(commentSpan);
            }
            commentSpan.textContent = commentCount + ' 评论';
        } else if (commentSpan) {
            commentSpan.remove();
        }
    }
    
    // ===== 发布动态功能 =====
    const publishInput = document.querySelector('.publish-input');
    const publishSubmit = document.querySelector('.publish-submit');
    
    if (publishSubmit) {
        publishSubmit.addEventListener('click', function() {
            const content = publishInput.value.trim();
            
            if (!content) {
                alert('请输入动态内容');
                publishInput.focus();
                return;
            }
            
            if (!isUserLoggedIn()) {
                alert('请先登录后再发布动态');
                return;
            }
            
            // 显示发布中状态
            const originalText = publishSubmit.textContent;
            publishSubmit.textContent = '发布中...';
            publishSubmit.disabled = true;
            
            // 模拟发布过程
            setTimeout(() => {
                // 创建新的动态元素
                createNewMoment(content);
                
                // 清空输入框
                publishInput.value = '';
                publishSubmit.textContent = originalText;
                publishSubmit.disabled = false;
                
                alert('动态发布成功！');
            }, 1000);
        });
    }
    
    function createNewMoment(content) {
        const momentsList = document.getElementById('moments-list');
        const noMoments = momentsList.querySelector('.no-moments');
        
        if (noMoments) {
            noMoments.remove();
        }
        
        const newMoment = document.createElement('article');
        newMoment.className = 'moment-item';
        newMoment.innerHTML = `
            <div class="moment-header">
                <div class="moment-avatar">
                    <img src="${getCurrentUserAvatar()}" alt="用户头像">
                </div>
                <div class="moment-user-info">
                    <div class="moment-nickname">${getCurrentUserName()}</div>
                    <div class="moment-time">刚刚</div>
                </div>
            </div>
            
            <div class="moment-content">${content}</div>
            
            <div class="moment-actions">
                <div class="moment-stats"></div>
                <div class="moment-interaction">
                    <button class="moment-like" data-moment-id="new" data-liked="false">
                        👍 赞
                    </button>
                    <button class="moment-comment" data-moment-id="new">
                        💬 评论
                    </button>
                </div>
            </div>
            
            <div class="moment-comment-input" style="display: none;">
                <input type="text" placeholder="评论..." class="comment-text">
                <button class="comment-submit">发送</button>
            </div>
        `;
        
        momentsList.insertBefore(newMoment, momentsList.firstChild);
    }
    
    // ===== 加载更多功能 =====
    const loadMoreBtn = document.getElementById('load-more-moments');
    let page = 2;
    
    if (loadMoreBtn) {
        loadMoreBtn.addEventListener('click', function() {
            loadMoreMoments();
        });
    }
    
    function loadMoreMoments() {
        const data = new FormData();
        data.append('action', 'load_more_moments');
        data.append('page', page);
        
        loadMoreBtn.textContent = '加载中...';
        loadMoreBtn.disabled = true;
        
        fetch(ajaxurl, {
            method: 'POST',
            body: data
        })
        .then(response => response.json())
        .then(result => {
            if (result.success && result.data.html) {
                document.getElementById('moments-list').insertAdjacentHTML('beforeend', result.data.html);
                page++;
                
                if (!result.data.has_more) {
                    loadMoreBtn.textContent = '没有更多动态了';
                    loadMoreBtn.disabled = true;
                } else {
                    loadMoreBtn.textContent = '加载更多';
                    loadMoreBtn.disabled = false;
                }
            } else {
                loadMoreBtn.textContent = '没有更多动态了';
                loadMoreBtn.disabled = true;
            }
        })
        .catch(error => {
            console.error('加载更多错误:', error);
            loadMoreBtn.textContent = '加载失败，点击重试';
            loadMoreBtn.disabled = false;
        });
    }
    
    // ===== 工具函数 =====
    function isUserLoggedIn() {
        // 这里应该检查用户是否登录
        // 实际使用时需要与WordPress用户系统集成
        return true; // 暂时返回true用于测试
    }
    
    function getCurrentUserAvatar() {
        // 获取当前用户头像
        return '/wp-content/themes/kam-inspired-theme/assets/images/default-avatar.png';
    }
    
    function getCurrentUserName() {
        // 获取当前用户名
        return '当前用户';
    }
    
    // ===== 键盘快捷键 =====
    document.addEventListener('keydown', function(e) {
        // ESC键关闭图片预览
        if (e.key === 'Escape' && imageModal.classList.contains('active')) {
            closeImageModal();
        }
        
        // Enter键提交评论（在评论输入框内）
        if (e.key === 'Enter' && e.target.classList.contains('comment-text')) {
            e.preventDefault();
            const submitBtn = e.target.closest('.moment-comment-input').querySelector('.comment-submit');
            if (e.target.value.trim()) {
                submitBtn.click();
            }
        }
        
        // Ctrl+Enter 发布动态
        if (e.key === 'Enter' && e.ctrlKey && document.activeElement === publishInput) {
            e.preventDefault();
            publishSubmit.click();
        }
    });
    
    // ===== 图片懒加载 =====
    function initLazyLoading() {
        const images = document.querySelectorAll('img[data-src]');
        
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.getAttribute('data-src');
                    img.removeAttribute('data-src');
                    img.classList.add('loaded');
                    imageObserver.unobserve(img);
                }
            });
        });
        
        images.forEach(img => imageObserver.observe(img));
    }
    
    // ===== 滚动动画 =====
    function initScrollAnimation() {
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };
        
        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                }
            });
        }, observerOptions);
        
        document.querySelectorAll('.moment-item').forEach(item => {
            item.classList.add('fade-in');
            observer.observe(item);
        });
    }
    
    // ===== 初始化所有功能 =====
    function initAll() {
        initLazyLoading();
        initScrollAnimation();
        
        // 添加CSS动画
        const style = document.createElement('style');
        style.textContent = `
            @keyframes fadeIn {
                from { opacity: 0; transform: translateY(20px); }
                to { opacity: 1; transform: translateY(0); }
            }
            
            .fade-in {
                opacity: 0;
                transform: translateY(20px);
                transition: opacity 0.6s ease, transform 0.6s ease;
            }
            
            .fade-in.visible {
                opacity: 1;
                transform: translateY(0);
            }
            
            .moment-image img.loaded {
                animation: fadeIn 0.5s ease-in;
            }
            
            .nav-toggle.active span:nth-child(1) {
                transform: rotate(45deg) translate(5px, 5px);
            }
            
            .nav-toggle.active span:nth-child(2) {
                opacity: 0;
            }
            
            .nav-toggle.active span:nth-child(3) {
                transform: rotate(-45deg) translate(7px, -6px);
            }
            
            .nav-toggle span {
                transition: all 0.3s ease;
            }
        `;
        document.head.appendChild(style);
    }
    
    // 执行初始化
    initAll();
});

// ===== 全局错误处理 =====
window.addEventListener('error', function(e) {
    console.error('JavaScript Error:', e.error);
});

// ===== 控制台欢迎信息 =====
if (console && console.log) {
    console.log(`
    🎨 KAM Inspired Theme v1.0 - 朋友圈版本
    🌐 微信朋友圈风格布局
    💡 支持点赞、评论、发布动态
    👥 真实的社交互动体验
    `);
}