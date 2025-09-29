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
    const publishTools = document.querySelectorAll('.publish-tool');
    const imagePreviewGrid = document.createElement('div');
    imagePreviewGrid.className = 'image-preview-grid';
    imagePreviewGrid.style.display = 'none';
    imagePreviewGrid.style.gridTemplateColumns = 'repeat(3, 1fr)';
    imagePreviewGrid.style.gap = '5px';
    imagePreviewGrid.style.marginTop = '10px';
    
    // 位置输入框
    const locationInput = document.createElement('div');
    locationInput.className = 'location-input';
    locationInput.style.display = 'none';
    locationInput.style.marginTop = '10px';
    locationInput.innerHTML = '<input type="text" placeholder="输入位置..." class="location-text">';
    
    // 将图片预览和位置输入添加到发布区域
    if (publishSubmit && publishInput) {
        publishInput.parentNode.insertBefore(imagePreviewGrid, publishInput.nextSibling);
        publishInput.parentNode.insertBefore(locationInput, imagePreviewGrid.nextSibling);
        
        publishSubmit.addEventListener('click', function() {
            publishMoment();
        });
        
        // 图片上传功能
        publishTools[0].addEventListener('click', function() {
            const fileInput = document.createElement('input');
            fileInput.type = 'file';
            fileInput.accept = 'image/*';
            fileInput.multiple = true;
            
            fileInput.addEventListener('change', function(e) {
                handleImageUpload(e.target.files);
            });
            
            fileInput.click();
        });
        
        // 表情选择功能
        publishTools[1].addEventListener('click', function() {
            const emojis = ['😊', '👍', '❤️', '🎉', '😂', '👏', '🤔', '😢', '😎', '😍', '🙏', '🎉', '🔥', '✨', '🌟'];
            let emojiPanel = document.querySelector('.emoji-panel');
            
            if (!emojiPanel) {
                emojiPanel = document.createElement('div');
                emojiPanel.className = 'emoji-panel';
                emojiPanel.style.position = 'absolute';
                emojiPanel.style.backgroundColor = 'white';
                emojiPanel.style.border = '1px solid #ddd';
                emojiPanel.style.borderRadius = '8px';
                emojiPanel.style.padding = '10px';
                emojiPanel.style.boxShadow = '0 2px 10px rgba(0,0,0,0.1)';
                emojiPanel.style.zIndex = '100';
                emojiPanel.style.display = 'flex';
                emojiPanel.style.flexWrap = 'wrap';
                emojiPanel.style.maxWidth = '250px';
                
                emojis.forEach(emoji => {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.style.fontSize = '1.5rem';
                    btn.style.border = 'none';
                    btn.style.background = 'none';
                    btn.style.cursor = 'pointer';
                    btn.style.margin = '5px';
                    btn.textContent = emoji;
                    
                    btn.addEventListener('click', function() {
                        publishInput.value += emoji;
                        emojiPanel.remove();
                    });
                    
                    emojiPanel.appendChild(btn);
                });
                
                this.parentNode.appendChild(emojiPanel);
                const rect = this.getBoundingClientRect();
                emojiPanel.style.top = `${rect.bottom + window.scrollY}px`;
                emojiPanel.style.left = `${rect.left + window.scrollX}px`;
            } else {
                emojiPanel.remove();
            }
        });
        
        // 位置功能
        publishTools[2].addEventListener('click', function() {
            if (locationInput.style.display === 'none') {
                locationInput.style.display = 'block';
                locationInput.querySelector('.location-text').focus();
            } else {
                locationInput.style.display = 'none';
            }
        });
    }
    
    // 处理图片上传预览
    function handleImageUpload(files) {
        if (!files.length) return;
        
        imagePreviewGrid.style.display = 'grid';
        
        // 限制最多9张图片
        const maxImages = 9;
        const currentImages = imagePreviewGrid.children.length;
        const imagesToProcess = Array.from(files).slice(0, maxImages - currentImages);
        
        imagesToProcess.forEach(file => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const imgContainer = document.createElement('div');
                imgContainer.style.position = 'relative';
                imgContainer.style.aspectRatio = '1/1';
                imgContainer.style.overflow = 'hidden';
                imgContainer.style.borderRadius = '8px';
                
                const img = document.createElement('img');
                img.src = e.target.result;
                img.style.width = '100%';
                img.style.height = '100%';
                img.style.objectFit = 'cover';
                
                // 删除按钮
                const deleteBtn = document.createElement('button');
                deleteBtn.textContent = '×';
                deleteBtn.style.position = 'absolute';
                deleteBtn.style.top = '2px';
                deleteBtn.style.right = '2px';
                deleteBtn.style.backgroundColor = 'rgba(0,0,0,0.5)';
                deleteBtn.style.color = 'white';
                deleteBtn.style.border = 'none';
                deleteBtn.style.borderRadius = '50%';
                deleteBtn.style.width = '20px';
                deleteBtn.style.height = '20px';
                deleteBtn.style.cursor = 'pointer';
                deleteBtn.style.display = 'flex';
                deleteBtn.style.alignItems = 'center';
                deleteBtn.style.justifyContent = 'center';
                
                deleteBtn.addEventListener('click', function() {
                    imgContainer.remove();
                    if (imagePreviewGrid.children.length === 0) {
                        imagePreviewGrid.style.display = 'none';
                    }
                });
                
                imgContainer.appendChild(img);
                imgContainer.appendChild(deleteBtn);
                imagePreviewGrid.appendChild(imgContainer);
            };
            reader.readAsDataURL(file);
        });
    }
    
    // 发布动态
    function publishMoment() {
        if (!isUserLoggedIn()) {
            alert('请先登录后再发布');
            return;
        }
        
        const content = publishInput.value.trim();
        const location = locationInput.querySelector('.location-text').value.trim();
        const images = [];
        
        // 收集图片数据
        Array.from(imagePreviewGrid.children).forEach(child => {
            const img = child.querySelector('img');
            if (img) {
                images.push(img.src);
            }
        });
        
        if (!content && images.length === 0) {
            alert('请输入内容或添加图片');
            return;
        }
        
        const data = new FormData();
        data.append('action', 'publish_moment');
        data.append('content', content);
        data.append('location', location);
        data.append('images', JSON.stringify(images));
        
        publishSubmit.disabled = true;
        publishSubmit.textContent = '发布中...';
        
        fetch(ajaxurl, {
            method: 'POST',
            body: data
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                // 发布成功，刷新页面或添加到列表
                location.reload();
            } else {
                alert(result.data || '发布失败');
            }
        })
        .catch(error => {
            console.error('发布错误:', error);
            alert('网络错误，请重试');
        })
        .finally(() => {
            publishSubmit.disabled = false;
            publishSubmit.textContent = '发布';
        });
    }
    
    // 添加MD5函数用于生成Gravatar头像哈希
function md5(str) {
    let hash = 0;
    if (str.length === 0) return hash;
    for (let i = 0; i < str.length; i++) {
        const char = str.charCodeAt(i);
        hash = ((hash << 5) - hash) + char;
        hash = hash & hash;
    }
    return Math.abs(hash).toString(16).padStart(32, '0');
}

// 修改评论提交事件处理
document.addEventListener('click', function(e) {
    // ... 保持其他代码不变 ...
    
    if (e.target.classList.contains('comment-submit')) {
        const button = e.target;
        const momentItem = button.closest('.moment-item');
        const momentId = momentItem.getAttribute('data-moment-id');
        const textInput = momentItem.querySelector('.comment-text');
        const nameInput = momentItem.querySelector('.comment-name');
        const emailInput = momentItem.querySelector('.comment-email');
        
        const content = textInput.value.trim();
        const name = nameInput.value.trim();
        const email = emailInput.value.trim();
        
        if (content && name && email) {
            submitComment(momentId, content, name, email, momentItem, button);
        } else {
            alert('请填写完整信息');
        }
    }
});

// 更新评论提交函数
function submitComment(momentId, content, name, email, momentItem, button) {
    // 检查用户是否登录
    if (!isUserLoggedIn()) {
        alert('请先登录后再评论');
        return;
    }
    
    // 简单邮箱验证
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        alert('请输入有效的邮箱地址');
        return;
    }
    
    const data = new FormData();
    data.append('action', 'moment_comment');
    data.append('moment_id', momentId);
    data.append('content', content);
    data.append('name', name);
    data.append('email', email);
    
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
            momentItem.querySelector('.comment-text').value = '';
            momentItem.querySelector('.comment-name').value = '';
            momentItem.querySelector('.comment-email').value = '';
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

// 更新评论显示函数，添加头像
function addCommentToDOM(comment, momentItem) {
    let commentsContainer = momentItem.querySelector('.moment-comments');
    if (!commentsContainer) {
        commentsContainer = document.createElement('div');
        commentsContainer.className = 'moment-comments';
        const actions = momentItem.querySelector('.moment-actions');
        actions.insertAdjacentElement('afterend', commentsContainer);
    }
    
    // 生成Gravatar头像URL
    const emailHash = md5(comment.email.toLowerCase());
    const avatarUrl = `https://www.gravatar.com/avatar/${emailHash}?d=identicon&s=36`;
    
    const commentItem = document.createElement('div');
    commentItem.className = 'moment-comment-item';
    commentItem.innerHTML = `
        <img src="${avatarUrl}" alt="${comment.author}" class="comment-avatar">
        <div class="comment-content">
            <span class="moment-comment-author">${comment.author}</span>
            <span class="moment-comment-text">${comment.content}</span>
        </div>
    `;
    
    commentsContainer.appendChild(commentItem);
    
    // 滚动到最新评论
    commentItem.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}
    
    // 检查用户是否登录
    function isUserLoggedIn() {
        return typeof kamUser !== 'undefined' && kamUser.loggedIn;
    }
});