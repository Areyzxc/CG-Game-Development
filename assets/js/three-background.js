/**
 * ==========================================================
 * File: three-background.js
 * 
 * Description:
 *   - Implements the dynamic, interactive Three.js background for the anchor page.
 *   - Features:
 *       • Animated floating code symbols and keywords for Bootstrap, C++, Python, Java, JavaScript, HTML, and CSS
 *       • Parallax camera movement based on mouse position
 *       • Floating lines, bulbs, badges, and rewards for visual depth
 *       • Interactive effects: hold-to-highlight, particle explosions, wave effects
 *       • Responsive resizing and smooth animation loop
 * 
 * Usage:
 *   - Included on the anchor page (and any page with <div id="three-container">).
 *   - Requires Three.js and a loaded font (window.codeGameFont).
 *   - Designed for Code Game's landing/anchor experience.
 * 
 * Author: [Santiago]
 * Last Updated: [June 17, 2025]
 * -- Code Gaming Team --
 * ==========================================================
 */

// Three.js Background Implementation
class ThreeBackground {
    constructor() {
        this.container = document.getElementById('three-container');
        this.scene = new THREE.Scene();
        this.camera = new THREE.PerspectiveCamera(75, window.innerWidth / window.innerHeight, 0.1, 1000);
        this.renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });
        this.codeElements = [];
        this.mouseX = 0;
        this.mouseY = 0;
        this.targetX = 0;
        this.targetY = 0;
        this.windowHalfX = window.innerWidth / 2;
        this.windowHalfY = window.innerHeight / 2;
        this.isHolding = false;
        this.holdTimer = null;
        this.holdDuration = 1000; // 1 second hold
        this.raycaster = new THREE.Raycaster();
        this.mouse = new THREE.Vector2();

        // Programming language colors and symbols with enhanced colors
        this.languageConfig = {
            cpp: {
                color: 0x00A4EF,    // Brighter C++ blue
                hoverColor: 0x00C4FF, // Glowing effect color
                symbols: ['#include', 'cout', 'cin', 'int', 'void', 'class', 'for', 'while', 'if', 'else', 'return', 'const', 'auto', 'template', 'namespace'],
                keywords: ['C++', 'STL', 'OOP', 'Pointer', 'Vector', 'Map', 'Set', 'Queue', 'Stack', 'Algorithm']
            },
            python: {
                color: 0x4B8BBE,    // Vibrant Python blue
                hoverColor: 0x5B9BCE, // Glowing effect color
                symbols: ['def', 'class', 'if', 'else', 'for', 'while', 'try', 'except', 'import', 'from', 'return', 'yield', 'async', 'await', 'lambda'],
                keywords: ['Python', 'List', 'Dict', 'Tuple', 'Set', 'Generator', 'Decorator', 'Context', 'Module', 'Package']
            },
            java: {
                color: 0xED8B00,    // Java orange
                hoverColor: 0xFF9A00, // Glowing effect color
                symbols: ['public', 'class', 'static', 'void', 'main', 'String', 'int', 'for', 'while', 'if', 'else', 'return', 'try', 'catch', 'finally'],
                keywords: ['Java', 'JVM', 'OOP', 'Interface', 'Abstract', 'Inheritance', 'Polymorphism', 'Encapsulation', 'Spring', 'Maven']
            },
            javascript: {
                color: 0xF7DF1E,    // JavaScript yellow
                hoverColor: 0xFFE066, // Glowing effect color
                symbols: ['function', 'const', 'let', 'var', 'if', 'else', 'for', 'while', 'return', 'try', 'catch', 'async', 'await', '=>', 'class'],
                keywords: ['JavaScript', 'ES6', 'Node.js', 'React', 'Vue', 'Angular', 'DOM', 'JSON', 'API', 'Promise']
            },
            bootstrap: {
                color: 0x7952B3,    // Bootstrap purple
                hoverColor: 0x8A63C6, // Glowing effect color
                symbols: ['container', 'row', 'col', 'btn', 'card', 'navbar', 'modal', 'alert', 'badge', 'progress', 'form', 'input', 'table', 'grid', 'flex'],
                keywords: ['Bootstrap', 'CSS', 'Framework', 'Responsive', 'Grid', 'Components', 'Utilities', 'Themes', 'Icons', 'Layout']
            },
            html: {
                color: 0xE34F26,    // HTML orange
                hoverColor: 0xF16529, // Glowing effect color
                symbols: ['<html>', '<head>', '<body>', '<div>', '<p>', '<h1>', '<img>', '<a>', '<form>', '<input>', '<button>', '<ul>', '<li>', '<table>', '<nav>'],
                keywords: ['HTML', 'Semantic', 'Accessibility', 'SEO', 'Structure', 'Elements', 'Attributes', 'Tags', 'Document', 'Markup']
            },
            css: {
                color: 0x1572B6,    // CSS blue
                hoverColor: 0x1E88E5, // Glowing effect color
                symbols: ['color', 'background', 'margin', 'padding', 'border', 'display', 'position', 'flex', 'grid', 'animation', 'transition', 'transform', 'box-shadow', 'font-size', 'text-align'],
                keywords: ['CSS', 'Styling', 'Layout', 'Responsive', 'Flexbox', 'Grid', 'Animations', 'Transitions', 'Selectors', 'Properties']
            }
        };

        // Add new configurations for floating elements
        this.floatingElements = {
            lines: [],
            bulbs: [],
            badges: [],
            rewards: []
        };

        // Colors for different elements
        this.elementColors = {
            lines: [0x00A4EF, 0x4B8BBE, 0x8993BE, 0xFFD700, 0xFF6B6B],
            bulbs: [0xFFD700, 0xFF6B6B, 0x4CAF50, 0x9C27B0],
            badges: [0xFFD700, 0xC0C0C0, 0xCD7F32], // Gold, Silver, Bronze
            rewards: [0xFFD700, 0xFF6B6B, 0x4CAF50, 0x9C27B0, 0x2196F3]
        };

        // Badge and reward configurations
        this.badgeConfig = {
            types: ['Beginner', 'Intermediate', 'Advanced', 'Master', 'Expert'],
            icons: ['🏆', '⭐', '👑', '💎', '🌟']
        };

        this.rewardConfig = {
            types: ['Speed', 'Accuracy', 'Consistency', 'Innovation', 'Teamwork'],
            icons: ['⚡', '🎯', '📈', '💡', '🤝']
        };

        this.init();
        this.animate();
        this.addEventListeners();
    }

    init() {
        // Setup renderer
        this.renderer.setSize(window.innerWidth, window.innerHeight);
        this.renderer.setPixelRatio(window.devicePixelRatio);
        this.container.appendChild(this.renderer.domElement);

        // Setup camera
        this.camera.position.z = 30;

        // Create code elements
        this.createCodeElements();

        // Add ambient light
        const ambientLight = new THREE.AmbientLight(0xffffff, 0.5);
        this.scene.add(ambientLight);

        // Add point light
        const pointLight = new THREE.PointLight(0xffffff, 1);
        pointLight.position.set(0, 0, 50);
        this.scene.add(pointLight);

        // Create floating elements
        this.createFloatingLines();
        this.createFloatingBulbs();
        this.createFloatingBadges();
        this.createFloatingRewards();
    }

    createCodeElements() {
        // Wait for font to be loaded
        if (!window.codeGameFont) {
            setTimeout(() => this.createCodeElements(), 100);
            return;
        }

        // Create elements for each language
        Object.entries(this.languageConfig).forEach(([lang, config]) => {
            // Create symbol elements
            config.symbols.forEach((symbol, index) => {
                this.createCodeElement(symbol, config.color, lang, index, config.symbols.length, 'symbol');
            });

            // Create keyword elements
            config.keywords.forEach((keyword, index) => {
                this.createCodeElement(keyword, config.color, lang, index, config.keywords.length, 'keyword');
            });
        });
    }

    createCodeElement(text, color, language, index, total, type) {
        const geometry = new THREE.TextGeometry(text, {
            font: window.codeGameFont,
            size: type === 'keyword' ? 0.7 : 0.5, // Slightly smaller for better visibility
            height: 0.05, // Thinner for more elegant look
            curveSegments: 24, // Smoother curves
            bevelEnabled: true,
            bevelThickness: 0.02,
            bevelSize: 0.01,
            bevelOffset: 0,
            bevelSegments: 8
        });

        // Center the geometry
        geometry.computeBoundingBox();
        const centerOffset = -0.5 * (geometry.boundingBox.max.x - geometry.boundingBox.min.x);
        geometry.translate(centerOffset, 0, 0);

        const material = new THREE.MeshPhongMaterial({
            color: color,
            transparent: true,
            opacity: 0.85,
            shininess: 80,
            specular: 0x666666,
            emissive: new THREE.Color(0x000000),
            emissiveIntensity: 0
        });

        const mesh = new THREE.Mesh(geometry, material);

        // Calculate initial position in a more spread out sphere
        const radius = 25 + Math.random() * 5; // More spread out
        const theta = (index / total) * Math.PI * 2;
        const phi = Math.acos(2 * Math.random() - 1);
        
        mesh.position.set(
            radius * Math.sin(phi) * Math.cos(theta),
            radius * Math.sin(phi) * Math.sin(theta),
            radius * Math.cos(phi)
        );

        // Enhanced animation properties
        mesh.userData = {
            language: language,
            type: type,
            originalPosition: mesh.position.clone(),
            originalColor: color,
            hoverColor: this.languageConfig[language].hoverColor,
            rotationSpeed: {
                x: (Math.random() - 0.5) * 0.005, // Slower rotation
                y: (Math.random() - 0.5) * 0.005,
                z: (Math.random() - 0.5) * 0.005
            },
            floatSpeed: Math.random() * 0.003 + 0.001, // Slower, more gentle floating
            floatOffset: Math.random() * Math.PI * 2,
            floatRadius: 1.5 + Math.random() * 1.5, // Smaller radius for subtler movement
            floatHeight: 0.3 + Math.random() * 0.7,
            pulseSpeed: 0.5 + Math.random() * 0.5,
            pulseOffset: Math.random() * Math.PI * 2
        };

        this.codeElements.push(mesh);
        this.scene.add(mesh);
    }

    createFloatingLines() {
        const lineCount = 20;
        for (let i = 0; i < lineCount; i++) {
            const points = [];
            const segments = 5;
            const length = 5 + Math.random() * 10;
            
            for (let j = 0; j <= segments; j++) {
                points.push(new THREE.Vector3(
                    j * (length / segments),
                    Math.sin(j * Math.PI) * 2,
                    0
                ));
            }

            const geometry = new THREE.BufferGeometry().setFromPoints(points);
            const material = new THREE.LineBasicMaterial({
                color: this.elementColors.lines[Math.floor(Math.random() * this.elementColors.lines.length)],
                transparent: true,
                opacity: 0.6
            });

            const line = new THREE.Line(geometry, material);
            
            // Random initial position
            line.position.set(
                (Math.random() - 0.5) * 100,
                (Math.random() - 0.5) * 100,
                (Math.random() - 0.5) * 50
            );

            // Animation properties
            line.userData = {
                speed: 0.2 + Math.random() * 0.3,
                waveSpeed: 0.5 + Math.random() * 0.5,
                waveAmplitude: 1 + Math.random() * 2,
                rotationSpeed: (Math.random() - 0.5) * 0.02
            };

            this.floatingElements.lines.push(line);
            this.scene.add(line);
        }
    }

    createFloatingBulbs() {
        const bulbCount = 10;
        for (let i = 0; i < bulbCount; i++) {
            const geometry = new THREE.SphereGeometry(0.5 + Math.random() * 0.5, 16, 16);
            const material = new THREE.MeshPhongMaterial({
                color: this.elementColors.bulbs[Math.floor(Math.random() * this.elementColors.bulbs.length)],
                transparent: true,
                opacity: 0.8,
                emissive: 0x111111,
                shininess: 100
            });

            const bulb = new THREE.Mesh(geometry, material);
            
            // Random initial position
            bulb.position.set(
                (Math.random() - 0.5) * 80,
                (Math.random() - 0.5) * 80,
                (Math.random() - 0.5) * 40
            );

            // Animation properties
            bulb.userData = {
                speed: 0.3 + Math.random() * 0.4,
                waveSpeed: 0.3 + Math.random() * 0.4,
                waveAmplitude: 2 + Math.random() * 3,
                pulseSpeed: 0.5 + Math.random() * 0.5,
                pulseMin: 0.8,
                pulseMax: 1.2
            };

            this.floatingElements.bulbs.push(bulb);
            this.scene.add(bulb);
        }
    }

    createFloatingBadges() {
        const badgeCount = 15;
        for (let i = 0; i < badgeCount; i++) {
            const geometry = new THREE.CircleGeometry(1, 32);
            const material = new THREE.MeshPhongMaterial({
                color: this.elementColors.badges[Math.floor(Math.random() * this.elementColors.badges.length)],
                transparent: true,
                opacity: 0.7,
                side: THREE.DoubleSide
            });

            const badge = new THREE.Mesh(geometry, material);
            
            // Random initial position
            badge.position.set(
                (Math.random() - 0.5) * 90,
                (Math.random() - 0.5) * 90,
                (Math.random() - 0.5) * 45
            );

            // Animation properties
            badge.userData = {
                speed: 0.2 + Math.random() * 0.3,
                waveSpeed: 0.4 + Math.random() * 0.4,
                waveAmplitude: 1.5 + Math.random() * 2,
                rotationSpeed: (Math.random() - 0.5) * 0.01,
                type: this.badgeConfig.types[Math.floor(Math.random() * this.badgeConfig.types.length)],
                icon: this.badgeConfig.icons[Math.floor(Math.random() * this.badgeConfig.icons.length)]
            };

            this.floatingElements.badges.push(badge);
            this.scene.add(badge);
        }
    }

    createFloatingRewards() {
        const rewardCount = 12;
        for (let i = 0; i < rewardCount; i++) {
            const geometry = new THREE.OctahedronGeometry(0.8, 0);
            const material = new THREE.MeshPhongMaterial({
                color: this.elementColors.rewards[Math.floor(Math.random() * this.elementColors.rewards.length)],
                transparent: true,
                opacity: 0.8,
                shininess: 100
            });

            const reward = new THREE.Mesh(geometry, material);
            
            // Random initial position
            reward.position.set(
                (Math.random() - 0.5) * 85,
                (Math.random() - 0.5) * 85,
                (Math.random() - 0.5) * 42
            );

            // Animation properties
            reward.userData = {
                speed: 0.25 + Math.random() * 0.35,
                waveSpeed: 0.35 + Math.random() * 0.45,
                waveAmplitude: 1.8 + Math.random() * 2.5,
                rotationSpeed: {
                    x: (Math.random() - 0.5) * 0.02,
                    y: (Math.random() - 0.5) * 0.02,
                    z: (Math.random() - 0.5) * 0.02
                },
                type: this.rewardConfig.types[Math.floor(Math.random() * this.rewardConfig.types.length)],
                icon: this.rewardConfig.icons[Math.floor(Math.random() * this.rewardConfig.icons.length)]
            };

            this.floatingElements.rewards.push(reward);
            this.scene.add(reward);
        }
    }

    animate() {
        requestAnimationFrame(this.animate.bind(this));

        const time = Date.now() * 0.001;

        // Update existing code elements
        this.codeElements.forEach(element => {
            // Smooth rotation
            element.rotation.x += element.userData.rotationSpeed.x;
            element.rotation.y += element.userData.rotationSpeed.y;
            element.rotation.z += element.userData.rotationSpeed.z;

            // Enhanced floating motion
            const floatX = Math.sin(time * element.userData.floatSpeed + element.userData.floatOffset) * element.userData.floatRadius;
            const floatY = Math.cos(time * element.userData.floatSpeed + element.userData.floatOffset) * element.userData.floatRadius;
            const floatZ = Math.sin(time * element.userData.floatSpeed * 0.5 + element.userData.floatOffset) * element.userData.floatHeight;

            // Add subtle pulsing effect
            const pulse = Math.sin(time * element.userData.pulseSpeed + element.userData.pulseOffset) * 0.1 + 0.9;
            element.scale.set(pulse, pulse, pulse);

            // Smooth position update
            element.position.x = element.userData.originalPosition.x + floatX;
            element.position.y = element.userData.originalPosition.y + floatY;
            element.position.z = element.userData.originalPosition.z + floatZ;

            // Keep elements within bounds with smooth transition
            const maxDistance = 35;
            const distance = element.position.length();
            if (distance > maxDistance) {
                const scale = maxDistance / distance;
                element.position.multiplyScalar(scale);
                element.userData.originalPosition.copy(element.position);
            }
        });

        // Animate floating lines
        this.floatingElements.lines.forEach(line => {
            const { speed, waveSpeed, rotationSpeed } = line.userData;
            
            // Wave motion
            line.position.y += Math.sin(time * waveSpeed) * 0.1;
            line.position.x += speed;
            line.rotation.z += rotationSpeed;

            // Reset position if too far
            if (line.position.x > 50) {
                line.position.x = -50;
                line.position.y = (Math.random() - 0.5) * 100;
            }
        });

        // Animate floating bulbs
        this.floatingElements.bulbs.forEach(bulb => {
            const { speed, waveSpeed, pulseSpeed, pulseMin, pulseMax } = bulb.userData;
            
            // Wave motion
            bulb.position.y += Math.sin(time * waveSpeed) * 0.15;
            bulb.position.x += speed;
            
            // Pulsing effect
            const pulse = pulseMin + (Math.sin(time * pulseSpeed) + 1) * 0.5 * (pulseMax - pulseMin);
            bulb.scale.set(pulse, pulse, pulse);

            // Reset position if too far
            if (bulb.position.x > 40) {
                bulb.position.x = -40;
                bulb.position.y = (Math.random() - 0.5) * 80;
            }
        });

        // Animate floating badges
        this.floatingElements.badges.forEach(badge => {
            const { speed, waveSpeed, rotationSpeed } = badge.userData;
            
            // Wave motion
            badge.position.y += Math.sin(time * waveSpeed) * 0.12;
            badge.position.x += speed;
            badge.rotation.z += rotationSpeed;

            // Reset position if too far
            if (badge.position.x > 45) {
                badge.position.x = -45;
                badge.position.y = (Math.random() - 0.5) * 90;
            }
        });

        // Animate floating rewards
        this.floatingElements.rewards.forEach(reward => {
            const { speed, waveSpeed, rotationSpeed } = reward.userData;
            
            // Wave motion
            reward.position.y += Math.sin(time * waveSpeed) * 0.13;
            reward.position.x += speed;
            
            // Rotation
            reward.rotation.x += rotationSpeed.x;
            reward.rotation.y += rotationSpeed.y;
            reward.rotation.z += rotationSpeed.z;

            // Reset position if too far
            if (reward.position.x > 42) {
                reward.position.x = -42;
                reward.position.y = (Math.random() - 0.5) * 85;
            }
        });

        // Smoother scene rotation
        this.targetX = (this.mouseX - this.windowHalfX) * 0.0003;
        this.targetY = (this.mouseY - this.windowHalfY) * 0.0003;

        this.scene.rotation.x += (this.targetY - this.scene.rotation.x) * 0.03;
        this.scene.rotation.y += (this.targetX - this.scene.rotation.y) * 0.03;

        // Enhanced hold interaction
        if (this.isHolding) {
            this.raycaster.setFromCamera(this.mouse, this.camera);
            const intersects = this.raycaster.intersectObjects(this.codeElements);

            if (intersects.length > 0) {
                const selectedElement = intersects[0].object;
                
                // Highlight selected element
                selectedElement.material.emissive.setHex(selectedElement.userData.hoverColor);
                selectedElement.material.emissiveIntensity = 0.5;
                selectedElement.scale.set(1.2, 1.2, 1.2);
                
                // Highlight related elements
                this.codeElements.forEach(element => {
                    if (element.userData.language === selectedElement.userData.language) {
                        element.material.emissive.setHex(element.userData.hoverColor);
                        element.material.emissiveIntensity = 0.2;
                        element.scale.set(1.1, 1.1, 1.1);
                    }
                });
            }
        } else {
            // Reset highlights
            this.codeElements.forEach(element => {
                element.material.emissiveIntensity = 0;
                element.scale.set(1, 1, 1);
            });
        }

        this.renderer.render(this.scene, this.camera);
    }

    addEventListeners() {
        // Mouse move
        document.addEventListener('mousemove', (event) => {
            this.mouseX = event.clientX;
            this.mouseY = event.clientY;
            this.mouse.x = (event.clientX / window.innerWidth) * 2 - 1;
            this.mouse.y = -(event.clientY / window.innerHeight) * 2 + 1;
        });

        // Mouse down
        document.addEventListener('mousedown', () => {
            this.isHolding = true;
            this.holdTimer = setTimeout(() => {
                if (this.isHolding) {
                    this.triggerHoldEffect();
                }
            }, this.holdDuration);
        });

        // Mouse up
        document.addEventListener('mouseup', () => {
            this.isHolding = false;
            clearTimeout(this.holdTimer);
            this.codeElements.forEach(element => {
                element.material.emissiveIntensity = 0;
            });
        });

        // Window resize
        window.addEventListener('resize', () => {
            this.camera.aspect = window.innerWidth / window.innerHeight;
            this.camera.updateProjectionMatrix();
            this.renderer.setSize(window.innerWidth, window.innerHeight);
            this.windowHalfX = window.innerWidth / 2;
            this.windowHalfY = window.innerHeight / 2;
        });
    }

    triggerHoldEffect() {
        // Create a wave effect from the mouse position
        const waveGeometry = new THREE.RingGeometry(0, 1, 32);
        const waveMaterial = new THREE.MeshBasicMaterial({
            color: 0xffffff,
            transparent: true,
            opacity: 0.5,
            side: THREE.DoubleSide
        });

        const wave = new THREE.Mesh(waveGeometry, waveMaterial);
        wave.position.set(
            (this.mouseX / window.innerWidth) * 100 - 50,
            -(this.mouseY / window.innerHeight) * 100 + 50,
            0
        );
        this.scene.add(wave);

        // Animate the wave
        const startTime = Date.now();
        const animateWave = () => {
            const elapsed = Date.now() - startTime;
            const progress = elapsed / 1000; // 1 second animation

            if (progress < 1) {
                wave.scale.set(progress * 10, progress * 10, 1);
                wave.material.opacity = 0.5 * (1 - progress);
                requestAnimationFrame(animateWave);
            } else {
                this.scene.remove(wave);
            }
        };

        animateWave();

        // Trigger particle explosion
        this.createParticleExplosion(
            (this.mouseX / window.innerWidth) * 100 - 50,
            -(this.mouseY / window.innerHeight) * 100 + 50
        );
    }

    createParticleExplosion(x, y) {
        const particleCount = 50;
        const geometry = new THREE.BufferGeometry();
        const positions = new Float32Array(particleCount * 3);
        const colors = new Float32Array(particleCount * 3);

        for (let i = 0; i < particleCount; i++) {
            const angle = (i / particleCount) * Math.PI * 2;
            const radius = Math.random() * 2;
            
            positions[i * 3] = x + Math.cos(angle) * radius;
            positions[i * 3 + 1] = y + Math.sin(angle) * radius;
            positions[i * 3 + 2] = 0;

            const colorKeys = Object.keys(this.languageConfig);
            const randomColor = this.languageConfig[colorKeys[Math.floor(Math.random() * colorKeys.length)]].color;
            const color = new THREE.Color(randomColor);
            colors[i * 3] = color.r;
            colors[i * 3 + 1] = color.g;
            colors[i * 3 + 2] = color.b;
        }

        geometry.setAttribute('position', new THREE.BufferAttribute(positions, 3));
        geometry.setAttribute('color', new THREE.BufferAttribute(colors, 3));

        const material = new THREE.PointsMaterial({
            size: 0.5,
            vertexColors: true,
            transparent: true,
            opacity: 0.8,
            blending: THREE.AdditiveBlending
        });

        const particles = new THREE.Points(geometry, material);
        this.scene.add(particles);

        // Animate explosion
        const startTime = Date.now();
        const animateExplosion = () => {
            const elapsed = Date.now() - startTime;
            const progress = elapsed / 1000;

            if (progress < 1) {
                const positions = particles.geometry.attributes.position.array;
                for (let i = 0; i < positions.length; i += 3) {
                    positions[i] += (positions[i] - x) * 0.1;
                    positions[i + 1] += (positions[i + 1] - y) * 0.1;
                }
                particles.geometry.attributes.position.needsUpdate = true;
                particles.material.opacity = 0.8 * (1 - progress);
                requestAnimationFrame(animateExplosion);
            } else {
                this.scene.remove(particles);
            }
        };

        animateExplosion();
    }
}

// Initialize Three.js background when the DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new ThreeBackground();
}); 